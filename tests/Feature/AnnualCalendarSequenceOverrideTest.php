<?php

namespace Tests\Feature;

use App\Models\AnnualCalendar;
use App\Models\AnnualCalendarActivity;
use App\Models\BatchSchedule;
use App\Models\TemplatePhase;
use App\Models\TrainingBatch;
use App\Models\TrainingTemplate;
use App\Models\Holiday;
use App\Models\WiAssignment;
use App\Models\Widyaiswara;
use App\Services\AnnualCalendarService;
use App\Services\ConflictCheckerService;
use App\Services\ScheduleGeneratorService;
use App\Services\WorkingDayService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnualCalendarSequenceOverrideTest extends TestCase
{
    use RefreshDatabase;

    private function makeTemplate(?string $conflictGroup = null): TrainingTemplate
    {
        $template = TrainingTemplate::create([
            'code' => 'PKA', 'name' => 'PKA', 'is_active' => true,
        ]);

        TemplatePhase::create([
            'training_template_id' => $template->id, 'name' => 'CekIn',
            'sequence' => 1, 'duration' => 1, 'duration_unit' => 'day',
            'day_type' => 'working_day', 'work_days' => 5,
        ]);

        TemplatePhase::create([
            'training_template_id' => $template->id, 'name' => 'On-Campus',
            'sequence' => 2, 'duration' => 3, 'duration_unit' => 'day',
            'day_type' => 'working_day', 'work_days' => 5,
            'conflict_group' => $conflictGroup,
        ]);

        return $template;
    }

    private function makeBatch(TrainingTemplate $template, string $startDate, string $name = 'Pelatihan A'): TrainingBatch
    {
        return TrainingBatch::create([
            'training_template_id' => $template->id,
            'batch_number' => 1, 'year' => (int) substr($startDate, 0, 4),
            'name' => $name, 'start_date' => $startDate,
            'participant_count' => 10, 'status' => 'draft',
        ]);
    }

    private function generator(): ScheduleGeneratorService
    {
        return app(ScheduleGeneratorService::class);
    }

    private function service(): AnnualCalendarService
    {
        return app(AnnualCalendarService::class);
    }

    /** 1. Sequence dibuat tanpa Kalender Tahunan tetap bisa generate. */
    public function test_batch_without_annual_calendar_generates_normally(): void
    {
        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11'); // Monday
        $this->generator()->generate($batch);

        $this->assertNull($batch->annual_calendar_activity_id);
        $this->assertSame(2, $batch->schedules()->count());
        $this->assertSame('generated', $batch->refresh()->status);
    }

    /** 2. Kalender Tahunan dibuat tanpa kegiatan. */
    public function test_annual_calendar_can_be_created_without_activities(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);

        $this->assertSame(0, $calendar->activities()->count());
    }

    /** 3. Kegiatan punya tanggal awal tapi belum dipakai di Sequence manapun — tidak mempengaruhi apa pun. */
    public function test_activity_with_planned_date_not_linked_has_no_effect(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create([
            'name' => 'Pelatihan A', 'planned_start_date' => '2027-01-10', 'planned_end_date' => '2027-01-18',
        ]);

        $batch = $this->makeBatch($this->makeTemplate(), '2027-03-01');
        $this->generator()->generate($batch);

        $this->assertNull($batch->refresh()->annual_calendar_activity_id);
        $this->assertSame('2027-03-01', $batch->start_date->toDateString());
        $this->assertNotNull($activity->id); // activity untouched, exists independently
    }

    /** 4 & 9. Override 10-18 Jan -> 10-18 Feb memindahkan seluruh Sequence (termasuk dependensi/phase berikutnya). */
    public function test_override_shifts_whole_sequence_without_touching_annual_plan(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create([
            'name' => 'Pelatihan A', 'planned_start_date' => '2027-01-11', 'planned_end_date' => '2027-01-19',
        ]);

        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11', 'Pelatihan A');
        $this->service()->linkActivity($batch, $activity);
        $this->generator()->generate($batch->refresh());

        $phase2Before = $batch->schedules()->orderBy('sequence')->get()->last();

        $this->service()->setOverride($batch->refresh(), '2027-02-08'); // Monday 8 Feb

        $batch->refresh();
        $this->assertSame('2027-02-08', $batch->start_date->toDateString());
        $this->assertTrue($batch->hasDateOverride());

        // dependent phase (sequence 2) shifted by the same delta, not left behind
        $phase2After = $batch->schedules()->orderBy('sequence')->get()->last();
        $this->assertSame(
            $phase2Before->start_date->copy()->addDays(28)->toDateString(),
            $phase2After->start_date->toDateString()
        );

        // annual calendar's own plan untouched (rule 4)
        $this->assertSame('2027-01-11', $activity->refresh()->planned_start_date->toDateString());
    }

    /**
     * Tanggal selesai anchor dihitung ulang dari durasi + aturan hari kerja (bukan naive
     * shift-by-delta), karena pergeseran hari-dalam-minggu bisa mengubah jumlah weekend
     * yang dilewati meski delta kalendernya sama.
     */
    public function test_override_recomputes_end_date_via_duration_and_working_day_rules(): void
    {
        $template = TrainingTemplate::create(['code' => 'PKA', 'name' => 'PKA', 'is_active' => true]);
        TemplatePhase::create([
            'training_template_id' => $template->id, 'name' => 'On-Campus',
            'sequence' => 1, 'duration' => 5, 'duration_unit' => 'day',
            'day_type' => 'working_day', 'work_days' => 5,
        ]);

        $batch = $this->makeBatch($template, '2027-01-11'); // Monday
        $this->generator()->generate($batch);

        $overrideStart = '2027-02-18'; // Thursday — different weekday alignment than Monday
        $this->service()->setOverride($batch->refresh(), $overrideStart);

        $anchor = $batch->schedules()->orderBy('sequence')->first();
        $this->assertSame($overrideStart, $anchor->start_date->toDateString());

        $expectedEnd = app(WorkingDayService::class)->computeEndDate(
            Carbon::parse($overrideStart), 5, 'working_day', 5
        );
        $this->assertSame($expectedEnd->toDateString(), $anchor->end_date->toDateString());

        // naive calendar-delta shift would have given a different (wrong) end date here
        $naiveShiftEnd = $anchor->start_date->copy()->addDays(
            Carbon::parse('2027-01-11')->diffInDays(Carbon::parse($overrideStart), false)
        );
        $this->assertNotSame($naiveShiftEnd->toDateString(), $expectedEnd->toDateString());
    }

    /** Hari libur yang jatuh dalam rentang hasil override tetap dihormati saat menghitung ulang tanggal selesai. */
    public function test_override_end_date_respects_holidays(): void
    {
        $template = TrainingTemplate::create(['code' => 'PKA', 'name' => 'PKA', 'is_active' => true]);
        TemplatePhase::create([
            'training_template_id' => $template->id, 'name' => 'On-Campus',
            'sequence' => 1, 'duration' => 3, 'duration_unit' => 'day',
            'day_type' => 'working_day', 'work_days' => 5,
        ]);

        $batch = $this->makeBatch($template, '2027-01-11'); // Monday
        $this->generator()->generate($batch);

        $overrideStart = '2027-03-01'; // Monday
        Holiday::create(['date' => '2027-03-02', 'name' => 'Libur Uji', 'is_national' => true]);

        $this->service()->setOverride($batch->refresh(), $overrideStart);

        $anchor = $batch->schedules()->orderBy('sequence')->first();

        // 3 working days from Mon 1 Mar, skipping the Tue 2 Mar holiday: Mon, Wed, Thu -> Thu 4 Mar
        $this->assertSame('2027-03-04', $anchor->end_date->toDateString());
    }

    /** 5. Generate ulang tidak mengembalikan tanggal ke Januari (override tidak tertimpa). */
    public function test_regenerate_does_not_revert_override(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create(['name' => 'Pelatihan A', 'planned_start_date' => '2027-01-11']);

        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->service()->linkActivity($batch, $activity);
        $this->generator()->generate($batch->refresh());
        $this->service()->setOverride($batch->refresh(), '2027-02-08');

        // re-generate (user clicks "Re-generate")
        $this->generator()->generate($batch->refresh());

        $this->assertSame('2027-02-08', $batch->refresh()->start_date->toDateString());
        $this->assertSame('2027-02-08', $batch->override_start_date->toDateString());
    }

    /** 6. Override diubah dari Februari menjadi Maret. */
    public function test_override_can_be_changed_again(): void
    {
        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->generator()->generate($batch);
        $this->service()->setOverride($batch->refresh(), '2027-02-08');
        $this->service()->setOverride($batch->refresh(), '2027-03-08');

        $this->assertSame('2027-03-08', $batch->refresh()->start_date->toDateString());
    }

    /** 7. Override dihapus -> tanggal rencana awal Kalender Tahunan kembali dipakai. */
    public function test_clearing_override_falls_back_to_annual_plan(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create(['name' => 'Pelatihan A', 'planned_start_date' => '2027-01-11']);

        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->service()->linkActivity($batch, $activity);
        $this->generator()->generate($batch->refresh());
        $this->service()->setOverride($batch->refresh(), '2027-02-08');

        $this->service()->clearOverride($batch->refresh());

        $batch->refresh();
        $this->assertNull($batch->override_start_date);
        $this->assertSame('2027-01-11', $batch->start_date->toDateString());
    }

    /** 8. Kegiatan tanpa tanggal rencana tetap bisa dijadwalkan lewat mekanisme Sequence biasa. */
    public function test_activity_without_planned_date_still_schedulable_via_sequence(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create(['name' => 'Pelatihan B']); // no planned date

        $batch = $this->makeBatch($this->makeTemplate(), '2027-04-05');
        $result = $this->service()->linkActivity($batch, $activity);

        $batch->refresh();
        $this->assertSame('2027-04-05', $batch->start_date->toDateString()); // untouched, rule 3 fallback
        $this->generator()->generate($batch);
        $this->assertSame(2, $batch->schedules()->count());
        $this->assertSame([], $result['seminar_conflicts']);
    }

    /** 10. Perubahan tanggal (override) memicu ulang conflict detection & alert. */
    public function test_override_recomputes_conflict_alert(): void
    {
        $template = $this->makeTemplate('seminar');

        $batchA = $this->makeBatch($template, '2027-01-11', 'Batch A');
        $this->generator()->generate($batchA);

        $batchB = $this->makeBatch($template, '2027-06-01', 'Batch B');
        $this->generator()->generate($batchB);

        $this->assertFalse($batchB->refresh()->has_conflict_alert);

        // Move batch B's seminar phase to overlap batch A's seminar phase
        $seminarA = $batchA->schedules()->where('conflict_group', 'seminar')->first();
        $this->service()->setOverride($batchB->refresh(), $seminarA->start_date->toDateString());

        $this->assertTrue($batchB->refresh()->has_conflict_alert);
    }

    /** 11. Aturan hari kerja/weekend/libur tetap berjalan untuk generate setelah fitur ditambahkan. */
    public function test_working_day_rules_still_applied_after_feature_added(): void
    {
        // start_date falls on a Saturday; working_day phase must land on next working day.
        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-09'); // Saturday
        $this->generator()->generate($batch);

        $first = $batch->schedules()->orderBy('sequence')->first();
        $this->assertFalse($first->start_date->isWeekend());
    }

    /** 12. Assignment & manual edit yang tidak terdampak tidak hilang saat override diterapkan. */
    public function test_override_preserves_locked_schedules_and_wi_assignments(): void
    {
        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->generator()->generate($batch);

        $schedules = $batch->schedules()->orderBy('sequence')->get();
        $second    = $schedules->last();
        $second->update(['is_locked' => true]);

        $wi = Widyaiswara::create(['nip' => '198001012020121001', 'name' => 'Dr. Siti', 'is_active' => true]);
        WiAssignment::create([
            'batch_schedule_id' => $second->id, 'widyaiswara_id' => $wi->id,
            'start_date' => $second->start_date, 'end_date' => $second->end_date,
        ]);

        $result = $this->service()->setOverride($batch->refresh(), '2027-02-08');

        // locked schedule reported as obstacle and NOT shifted
        $this->assertNotEmpty($result['locked_obstacles']);
        $second->refresh();
        $this->assertSame($schedules->last()->start_date->toDateString(), $second->start_date->toDateString());

        // its WI assignment is preserved and untouched too
        $this->assertDatabaseHas('wi_assignments', [
            'batch_schedule_id' => $second->id,
            'widyaiswara_id'    => $wi->id,
        ]);
    }

    /** 13. Menghapus Kalender Tahunan tidak menghapus Sequence/jadwal/assignment. */
    public function test_deleting_annual_calendar_does_not_delete_operational_data(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create(['name' => 'Pelatihan A', 'planned_start_date' => '2027-01-11']);

        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->service()->linkActivity($batch, $activity);
        $this->generator()->generate($batch->refresh());

        $scheduleId = $batch->schedules()->first()->id;

        $calendar->delete();

        $this->assertDatabaseHas('training_batches', ['id' => $batch->id]);
        $this->assertDatabaseHas('batch_schedules', ['id' => $scheduleId]);
        $this->assertDatabaseMissing('annual_calendar_activities', ['id' => $activity->id]);
        $this->assertNull($batch->refresh()->annual_calendar_activity_id);
    }

    /** 13b. Arsip Kalender Tahunan juga tidak menyentuh data operasional. */
    public function test_archiving_annual_calendar_keeps_everything_intact(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender 2027']);
        $activity = $calendar->activities()->create(['name' => 'Pelatihan A', 'planned_start_date' => '2027-01-11']);
        $batch    = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->service()->linkActivity($batch, $activity);

        $calendar->update(['archived_at' => now()]);

        $this->assertTrue($calendar->refresh()->isArchived());
        $this->assertSame($activity->id, $batch->refresh()->annual_calendar_activity_id);
    }

    /** 14. Data lama (batch tanpa kolom baru terisi) tetap kompatibel setelah migrasi. */
    public function test_legacy_batch_without_new_columns_remains_compatible(): void
    {
        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');

        $this->assertNull($batch->annual_calendar_activity_id);
        $this->assertNull($batch->override_start_date);
        $this->assertFalse($batch->hasDateOverride());

        $this->generator()->generate($batch);
        $this->assertSame('generated', $batch->refresh()->status);
    }

    public function test_conflict_checker_has_any_batch_conflict_still_usable(): void
    {
        $batch = $this->makeBatch($this->makeTemplate(), '2027-01-11');
        $this->generator()->generate($batch);

        $this->assertFalse(app(ConflictCheckerService::class)->hasAnyBatchConflict($batch->refresh()));
    }
}
