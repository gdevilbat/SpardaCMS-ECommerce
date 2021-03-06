<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EcommerceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(EcommerceModuleTableSeeder::class);
        $this->call(TaxonomyTableSeeder::class);
        $this->call(EcommerceTableSeeder::class);
    }
}
