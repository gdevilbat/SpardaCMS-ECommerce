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

        $id = DB::table('posts')->insertGetId(
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
        );

        DB::table('product_meta')->insert([
            [
                'product_id' => $id,
                'product_price' => 100000,
                'created_at' => \Carbon\Carbon::now()
            ]
        ]);

        DB::table('term_relationships')->insert([
            [
                'term_taxonomy_id' => 3,
                'object_id' => $id,
                'created_at' => \Carbon\Carbon::now()
            ]
        ]);
    }
}
