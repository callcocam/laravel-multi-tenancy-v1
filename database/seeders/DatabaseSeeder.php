<?php

namespace Database\Seeders;

use Call\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Tenant::checkCurrent()
            ? $this->runTenantSpecificSeeders()
            : $this->runLandlordSpecificSeeders();
    }

    public function runTenantSpecificSeeders()
    {
        \App\Models\User::factory(1)->create();
    }

    public function runLandlordSpecificSeeders()
    {
        $this->call(TenantSeeder::class);
        //$this->call(RoleSeeder::class);
        //$this->call(MenuSeeder::class);
        \App\Models\User::factory(1)->create();
    }
}
