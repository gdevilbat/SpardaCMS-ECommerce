<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

class EcommerceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $faker = \Faker\Factory::create();

        DB::table('posts')->insert([
            [
                'post_title' => $faker->word,
                'post_slug' => str_slug($faker->word),
                'post_content' => $faker->text,
                'post_excerpt' => $faker->word,
                'post_status' => 'draft',
                'post_type' => 'product',
                'created_by' => 1,
                'modified_by' => 1,
                'created_at' => \Carbon\Carbon::now()
            ]
        ]);
    }
}
