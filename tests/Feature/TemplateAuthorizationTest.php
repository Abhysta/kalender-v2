<?php

namespace Tests\Feature;

use App\Http\Controllers\TemplateController;
use App\Models\OrganizationalUnit;
use App\Models\TrainingTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TemplateAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression: store() already forced organizational_unit_id for non-super-admins,
     * but update() didn't — a non-super-admin could move a template to another unit
     * simply by submitting a different organizational_unit_id on the edit form.
     */
    public function test_non_super_admin_cannot_change_template_unit_via_update(): void
    {
        $unitA = OrganizationalUnit::create(['code' => 'UA', 'name' => 'Unit A', 'is_active' => true]);
        $unitB = OrganizationalUnit::create(['code' => 'UB', 'name' => 'Unit B', 'is_active' => true]);

        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['is_active' => true, 'organizational_unit_id' => $unitA->id]);
        $admin->assignRole('admin');
        Auth::login($admin);

        $template = TrainingTemplate::create([
            'code' => 'UNITFIX', 'name' => 'UNITFIX', 'organizational_unit_id' => $unitA->id, 'is_active' => true,
        ]);

        $request = Request::create('/x', 'PUT', [
            'code' => 'UNITFIX', 'name' => 'UNITFIX', 'organizational_unit_id' => $unitB->id, 'is_active' => '1',
        ]);
        app(TemplateController::class)->update($request, $template);

        $this->assertSame($unitA->id, $template->refresh()->organizational_unit_id);
    }

    public function test_super_admin_can_change_template_unit_via_update(): void
    {
        $unitA = OrganizationalUnit::create(['code' => 'UA2', 'name' => 'Unit A2', 'is_active' => true]);
        $unitB = OrganizationalUnit::create(['code' => 'UB2', 'name' => 'Unit B2', 'is_active' => true]);

        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create(['is_active' => true]);
        $superAdmin->assignRole('super_admin');
        Auth::login($superAdmin);

        $template = TrainingTemplate::create([
            'code' => 'UNITSA', 'name' => 'UNITSA', 'organizational_unit_id' => $unitA->id, 'is_active' => true,
        ]);

        $request = Request::create('/x', 'PUT', [
            'code' => 'UNITSA', 'name' => 'UNITSA', 'organizational_unit_id' => $unitB->id, 'is_active' => '1',
        ]);
        app(TemplateController::class)->update($request, $template);

        $this->assertSame($unitB->id, $template->refresh()->organizational_unit_id);
    }
}
