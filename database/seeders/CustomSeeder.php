<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $sql = file_get_contents(database_path() . '/data/settings.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/theme.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/hospitals.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/equipments.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/costcenters.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/countries.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/users.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/staff.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/roles.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/role_user.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/modules.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/permissions.sql');
        DB::statement($sql);
        $sql = file_get_contents(database_path() . '/data/permission_role.sql');
        DB::statement($sql);
    }

}
