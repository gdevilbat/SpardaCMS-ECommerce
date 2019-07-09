<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReadCategory()
    {
        $response = $this->get(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); // Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                         ->json('GET',action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@serviceMaster'))
                         ->assertSuccessful()
                         ->assertJsonStructure(['data', 'draw', 'recordsTotal', 'recordsFiltered']); // Return Valid user Login
    }

    public function testCreateDataCategory()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@store'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@create'))
                         ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@store'))
                         ->assertStatus(302)
                         ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@create'))
                         ->assertSessionHasErrors(); //Return Not Valid, Data Not Complete

        $faker = \Faker\Factory::create();
        $name = $faker->word;
        $slug = $faker->word;

        $taxonomy = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::with('term')->first();

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@create'))
                         ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@store'), [
                                'term' => ['name' => $name, 'slug' => $slug],
                                'taxonomy' => ['description' => $faker->text, 'taxonomy' => 'product-category', 'parent_id' => $taxonomy->getKey()],
                            ])
                         ->assertStatus(302)
                         ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                         ->assertSessionHas('global_message.status', 200)
                         ->assertSessionHasNoErrors(); //Return Valid, Data Complete

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::getTableName(), ['slug' => $slug]);

        $term = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::latest(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::getPrimaryKey())->first();

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getTableName(), ['term_id' => $term->getKey(), 'taxonomy' => 'product-category']);
    }

    public function testUpdateDataCategory()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@store'), [
                        '_method' => 'PUT'
                    ]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::with('role')->find(1);

        $faker = \Faker\Factory::create();

        $taxonomy = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::with('term')->first();

        $response = $this->actingAs($user)
                        ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@create').'?code='.encrypt($taxonomy->getKey()))
                        ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@store'), [
                            'term' => ['name' => $taxonomy->term->name, 'slug' => $taxonomy->term->slug],
                            'taxonomy' => ['description' => $faker->text, 'taxonomy' => 'category', 'parent_id' => $taxonomy->getKey()],
                            $taxonomy->getKeyName() => encrypt($taxonomy->getKey()),
                            '_method' => 'PUT'
                        ])
                        ->assertStatus(302)
                        ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                        ->assertSessionHas('global_message.status', 200)
                        ->assertSessionHasNoErrors(); //Return Valid, Data Complete
    }

    public function testDeleteDataCategory()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@destroy'), [
                        '_method' => 'DELETE'
                    ]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \App\User::find(1);

        $taxonomy = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::first();

        $response = $this->actingAs($user)
                        ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                        ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@destroy'), [
                            $taxonomy->getKeyName() => encrypt($taxonomy->getKey()),
                            '_method' => 'DELETE'
                        ])
                        ->assertStatus(302)
                        ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                        ->assertSessionHas('global_message.status', 200);

        $this->assertDatabaseMissing(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getTableName(), [$taxonomy->getKeyName() => $taxonomy->getKey()]);
    }
}
