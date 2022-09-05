<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Auth\RoleModulePermission;

class GodSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RoleModulePermission::where('role_id', 1)->delete();
        DB::statement('INSERT INTO role_module_permissions (role_id, module_permission, created_at, updated_at)
                SELECT 1, name, NOW(), NOW() FROM module_permissions');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
