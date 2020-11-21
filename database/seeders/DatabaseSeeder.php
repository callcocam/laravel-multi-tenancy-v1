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
        $this->call(\Database\Seeders\TenantSeeder::class);
    }

    public function runLandlordSpecificSeeders()
    {
        $this->call(\Database\Seeders\LandLordSeeder::class);
        //$this->call(RoleSeeder::class);
        //$this->call(MenuSeeder::class);
        \Call\Tenant\Models\LandlordUser::factory(1)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@landlord.com',
            'owner' => true,
        ]);
    }
}
