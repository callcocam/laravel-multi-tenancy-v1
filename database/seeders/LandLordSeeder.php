<?php

namespace Database\Seeders;

use Call\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class LandLordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tenant::query()->delete();

        $tenants = [
            [
                'name'=> 'SERVER',
                'domain'=>request()->getHost(),
                'database'=>'landlord',
                'prefix'=>'landlord',
                'middleware'=>['landlord'],
            ],
            [
                'name'=> 'Client 01',
                'domain'=> sprintf('client-01.%s',str_replace('www.','',request()->getHost())),
                'database'=>'tenants',
                'prefix'=>'admin',
                'middleware'=>['tenant'],
            ],
            [
                'name'=> 'Client 02',
                'domain'=>sprintf('client-02.%s',str_replace('www.','',request()->getHost())),
                'database'=>'tenants',
                'prefix'=>'admin',
                'middleware'=>['tenant'],
            ],
            [
                'name'=> 'Client 03',
                'domain'=>sprintf('client-03.%s',str_replace('www.','',request()->getHost())),
                'database'=>'tenants',
                'prefix'=>'admin',
                'middleware'=>['tenant'],
            ]
        ];
        foreach ($tenants as $value):
            Tenant::factory(1)->create($value);
        endforeach;
    }
}
