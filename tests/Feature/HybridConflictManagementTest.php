<?php

namespace Tests\Feature;

use App\Http\Controllers\TemplateController;
use App\Models\AnnualCalendar;
use App\Models\ConflictAlert;
use App\Models\ConflictGroup;
use App\Models\ConflictRule;
use App\Models\Holiday;
use App\Models\SequenceCatalog;
use App\Models\TemplatePhase;
use App\Models\TrainingBatch;
use App\Models\TrainingTemplate;
use App\Models\User;
use App\Services\AnnualCalendarService;
use App\Services\ConflictAlertService;
use App\Services\ScheduleGeneratorService;
use App\Services\SequenceCatalogService;
use App\Services\WorkingDayService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class HybridConflictManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeTemplate(string $code, string $phaseName = 'Seminar', ?string $conflictGroup = 'seminar'): TrainingTemplate
    {
        $template = TrainingTemplate::create(['code' => $code, 'name' => $code, 'is_active' => true]);
        TemplatePhase::create([
            'training_template_id' => $template->id, 'name' => $phaseName,
            'sequence' => 1, 'duration' => 3, 'duration_unit' => 'day',
            'day_type' => 'working_day', 'work_days' => 5, 'conflict_group' => $conflictGroup,
        ]);
        return $template;
    }

    private function makeBatch(TrainingTemplate $template, string $startDate, string $name = 'Batch'): TrainingBatch
    {
        return TrainingBatch::create([
            'training_template_id' => $template->id, 'batch_number' => 1,
            'year' => (int) substr($startDate, 0, 4), 'name' => $name,
            'start_date' => $startDate, 'participant_count' => 5, 'status' => 'draft',
        ]);
    }

    private function catalog(): SequenceCatalogService
    {
        return app(SequenceCatalogService::class);
    }

    /** 1. An approved (automatically resolved) mapping is reused for a subsequent import. */
    public function test_exact_match_reuses_canonical_catalog_entry(): void
    {
        $r1 = $this->catalog()->resolve('Seminar', 'seminar');
        $r2 = $this->catalog()->resolve('Seminar', 'seminar');

        $this->assertSame($r1['catalog']->id, $r2['catalog']->id);
        $this->assertSame(1, SequenceCatalog::count());
    }

    /** 2. Different Excel names (case/whitespace variants) map to the same canonical sequence automatically. */
    public function test_name_variants_resolve_to_same_canonical_sequence_and_learn_group(): void
    {
        $first  = $this->catalog()->resolve('Seminar', 'seminar');
        $second = $this->catalog()->resolve('  SEMINAR  ', null); // no explicit group this time

        $this->assertSame($first['catalog']->id, $second['catalog']->id);
        $this->assertNotNull($second['group']);
        $this->assertSame('seminar', $second['group']->code);
        $this->assertSame(1, SequenceCatalog::count());
        $this->assertSame(1, ConflictGroup::count());
    }

    /** 3. A genuinely unfamiliar name creates its own separate canonical identity (no merge). */
    public function test_unknown_name_creates_new_canonical_sequence_without_merging(): void
    {
        $a = $this->catalog()->resolve('Seminar', 'seminar');
        $b = $this->catalog()->resolve('On-Campus', null);

        $this->assertNotSame($a['catalog']->id, $b['catalog']->id);
        $this->assertNull($b['group']); // no explicit code, no learned default -> safely ungrouped
        $this->assertSame(2, SequenceCatalog::count());
    }

    /** 4. Similar-but-distinct names stay separate — no fuzzy merge regardless of similarity. */
    public function test_similar_names_do_not_silently_merge(): void
    {
        $a = $this->catalog()->resolve('Seminar Nasional', 'seminar');
        $b = $this->catalog()->resolve('Seminar', 'seminar'); // superficially similar, not identical

        $this->assertNotSame($a['catalog']->id, $b['catalog']->id);
        $this->assertSame(2, SequenceCatalog::count());
        // but both still land in the same explicit conflict group — grouping is explicit, not name-based
        $this->assertSame($a['group']->id, $b['group']->id);
    }

    /** 5/7. Duplicate imports (retry) do not create duplicate canonical sequences or groups. */
    public function test_retrying_the_same_import_is_idempotent_for_mappings(): void
    {
        $csv = "sequence,name,duration,duration_unit,day_type,work_days,offset_days,activity_type,conflict_group,is_alert\n"
             . "1,Seminar,3,day,working_day,5,0,seminar,seminar,0\n";

        $this->importCsv('IDEMP1', $csv);
        $this->importCsv('IDEMP2', $csv);

        $this->assertSame(1, SequenceCatalog::where('normalized_name', 'seminar')->count());
        $this->assertSame(1, ConflictGroup::where('code', 'seminar')->count());
        // two templates were legitimately created (that's expected — only the mapping layer is idempotent)
        $this->assertSame(2, TrainingTemplate::whereIn('code', ['IDEMP1', 'IDEMP2'])->count());
    }

    /** 6. Unauthorized users cannot reach conflict-group / rule management routes. */
    public function test_unauthorized_user_cannot_manage_conflict_groups(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        // no role/permission assigned

        $response = $this->actingAs($user)->get('/admin/conflict-groups');

        $response->assertForbidden();
    }

    /** 8. A conflict alert is created when an enabled rule (seminar overlap) is violated. */
    public function test_conflict_alert_created_for_seminar_overlap(): void
    {
        $t1 = $this->makeTemplate('CONFA');
        $t2 = $this->makeTemplate('CONFB');
        $batchA = $this->makeBatch($t1, '2027-03-01', 'Batch A');
        $batchB = $this->makeBatch($t2, '2027-03-02', 'Batch B'); // overlaps A's 3-day seminar

        $generator = app(ScheduleGeneratorService::class);
        $generator->generate($batchA);
        $generator->generate($batchB);

        $this->assertSame(1, ConflictAlert::where('status', 'open')->count());
        $alert = ConflictAlert::first();
        $this->assertSame('group_overlap', $alert->rule_type);
    }

    /** Detection is driven by the configured conflict group, not a hardcoded 'seminar' literal. */
    public function test_conflict_detection_works_for_any_configured_group(): void
    {
        $t1 = $this->makeTemplate('GRPA', 'Ruang Aula', 'ruang-aula');
        $t2 = $this->makeTemplate('GRPB', 'Ruang Aula', 'ruang-aula');
        $batchA = $this->makeBatch($t1, '2027-03-01', 'Batch A');
        $batchB = $this->makeBatch($t2, '2027-03-02', 'Batch B');

        $generator = app(ScheduleGeneratorService::class);
        $generator->generate($batchA);
        $generator->generate($batchB);

        $this->assertSame(1, ConflictAlert::where('status', 'open')->count());

        // a different, unrelated group never conflicts with it
        $t3 = $this->makeTemplate('GRPC', 'Ruang Lain', 'ruang-lain');
        $batchC = $this->makeBatch($t3, '2027-03-02', 'Batch C');
        $generator->generate($batchC);

        $this->assertSame(1, ConflictAlert::where('status', 'open')->count());
    }

    /** 9. Repeated evaluations do not duplicate the same alert. */
    public function test_repeated_evaluation_does_not_duplicate_alerts(): void
    {
        $t1 = $this->makeTemplate('DUPA');
        $t2 = $this->makeTemplate('DUPB');
        $batchA = $this->makeBatch($t1, '2027-04-01', 'Batch A');
        $batchB = $this->makeBatch($t2, '2027-04-02', 'Batch B');

        $generator = app(ScheduleGeneratorService::class);
        $generator->generate($batchA);
        $generator->generate($batchB);
        $generator->generate($batchB); // regenerate again
        app(ConflictAlertService::class)->evaluateBatch($batchB->refresh()); // evaluate directly again too

        $this->assertSame(1, ConflictAlert::count());
    }

    /** 10. An alert is resolved automatically once its underlying conflict disappears. */
    public function test_alert_auto_resolves_when_conflict_disappears(): void
    {
        $t1 = $this->makeTemplate('RESA');
        $t2 = $this->makeTemplate('RESB');
        $batchA = $this->makeBatch($t1, '2027-05-01', 'Batch A');
        $batchB = $this->makeBatch($t2, '2027-05-02', 'Batch B');

        $generator = app(ScheduleGeneratorService::class);
        $generator->generate($batchA);
        $generator->generate($batchB);
        $this->assertSame(1, ConflictAlert::where('status', 'open')->count());

        app(AnnualCalendarService::class)->setOverride($batchB->refresh(), '2027-09-01');

        $this->assertSame(0, ConflictAlert::where('status', 'open')->count());
        $this->assertSame(1, ConflictAlert::where('status', 'resolved')->count());
    }

    /** 11. Dependency-based date recalculation keeps working (sequence/offset untouched by this feature). */
    public function test_dependency_recalculation_still_works(): void
    {
        $template = TrainingTemplate::create(['code' => 'DEPREC', 'name' => 'DEPREC', 'is_active' => true]);
        TemplatePhase::create(['training_template_id' => $template->id, 'name' => 'CekIn', 'sequence' => 1, 'duration' => 1, 'duration_unit' => 'day', 'day_type' => 'working_day', 'work_days' => 5]);
        TemplatePhase::create(['training_template_id' => $template->id, 'name' => 'On-Campus', 'sequence' => 2, 'duration' => 3, 'duration_unit' => 'day', 'day_type' => 'working_day', 'work_days' => 5]);

        $batch = $this->makeBatch($template, '2027-01-11');
        app(ScheduleGeneratorService::class)->generate($batch);

        $phase2Before = $batch->schedules()->orderBy('sequence')->get()->last();
        app(AnnualCalendarService::class)->setOverride($batch->refresh(), '2027-02-08');
        $phase2After = $batch->schedules()->orderBy('sequence')->get()->last();

        $this->assertSame(
            $phase2Before->start_date->copy()->addDays(28)->toDateString(),
            $phase2After->start_date->toDateString()
        );
    }

    /** 12. Annual calendar overrides remain intact (effective-date priority unaffected by this feature). */
    public function test_annual_calendar_override_remains_intact(): void
    {
        $calendar = AnnualCalendar::create(['year' => 2027, 'name' => 'Kalender Hybrid']);
        $activity = $calendar->activities()->create(['name' => 'Pelatihan Hybrid', 'planned_start_date' => '2027-01-11']);

        $template = $this->makeTemplate('ANNUAL');
        $batch    = $this->makeBatch($template, '2027-01-11');
        app(AnnualCalendarService::class)->linkActivity($batch, $activity);
        app(ScheduleGeneratorService::class)->generate($batch->refresh());
        app(AnnualCalendarService::class)->setOverride($batch->refresh(), '2027-02-08');

        $this->assertSame('2027-02-08', $batch->refresh()->start_date->toDateString());
        $this->assertSame('2027-01-11', $activity->refresh()->planned_start_date->toDateString());
    }

    /** 13. Weekend and holiday adjustments still go through WorkingDayService. */
    public function test_holiday_and_weekend_rules_still_use_working_day_service(): void
    {
        $template = $this->makeTemplate('HOLI', 'On-Campus', null);
        Holiday::create(['date' => '2027-01-13', 'name' => 'Libur Uji', 'is_national' => true]);

        $batch = $this->makeBatch($template, '2027-01-11'); // Monday
        app(ScheduleGeneratorService::class)->generate($batch);

        $expectedEnd = app(WorkingDayService::class)->computeEndDate(Carbon::parse('2027-01-11'), 3, 'working_day', 5);
        $schedule = $batch->schedules()->first();

        $this->assertSame($expectedEnd->toDateString(), $schedule->end_date->toDateString());
    }

    /** 14. Existing schedule-recalculation + has_conflict_alert flow is untouched (regression guard). */
    public function test_existing_batch_without_catalog_data_still_generates(): void
    {
        $template = $this->makeTemplate('LEGACY', 'Legacy Phase', null);
        $batch    = $this->makeBatch($template, '2027-06-01');

        app(ScheduleGeneratorService::class)->generate($batch);

        $this->assertSame('generated', $batch->refresh()->status);
        $this->assertFalse($batch->has_conflict_alert);
    }

    /** 15. Disabling a rule stops it from producing new alerts. */
    public function test_disabled_rule_does_not_produce_alerts(): void
    {
        ConflictRule::where('code', 'group_overlap')->update(['is_active' => false]);

        $t1 = $this->makeTemplate('OFFA');
        $t2 = $this->makeTemplate('OFFB');
        $batchA = $this->makeBatch($t1, '2027-07-01', 'Batch A');
        $batchB = $this->makeBatch($t2, '2027-07-02', 'Batch B');

        $generator = app(ScheduleGeneratorService::class);
        $generator->generate($batchA);
        $generator->generate($batchB);

        $this->assertSame(0, ConflictAlert::count());
    }

    private function importCsv(string $code, string $csv): void
    {
        $user = User::factory()->create(['is_active' => true]);
        Auth::login($user);

        $file = UploadedFile::fake()->createWithContent('phases.csv', $csv);
        $request = Request::create('/admin/templates/import', 'POST', [
            'code' => $code, 'name' => $code,
        ], [], ['file' => $file]);
        $request->setUserResolver(fn () => $user);

        app(TemplateController::class)->import($request);
    }
}
