<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReadTag()
    {
        $response = $this->get(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); // Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                         ->json('GET',action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@serviceMaster'))
                         ->assertSuccessful()
                         ->assertJsonStructure(['data', 'draw', 'recordsTotal', 'recordsFiltered']); // Return Valid user Login
    }

    public function testCreateDataTag()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@store'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@create'))
                         ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@store'))
                         ->assertStatus(302)
                         ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@create'))
                         ->assertSessionHasErrors(); //Return Not Valid, Data Not Complete

        $faker = \Faker\Factory::create();
        $name = $faker->word;
        $slug = $faker->word;

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@create'))
                         ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@store'), [
                                'term' => ['name' => $name, 'slug' => $slug],
                                'taxonomy' => ['description' => $faker->text, 'taxonomy' => 'tag'],
                            ])
                         ->assertStatus(302)
                         ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                         ->assertSessionHas('global_message.status', 200)
                         ->assertSessionHasNoErrors(); //Return Valid, Data Complete

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::getTableName(), ['slug' => $slug]);

        $term = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::latest(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms::getPrimaryKey())->first();

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getTableName(), ['term_id' => $term->getKey(), 'taxonomy' => 'tag']);
    }

    public function testUpdateDataTag()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@store'), [
                        '_method' => 'PUT'
                    ]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::with('role')->find(1);

        $faker = \Faker\Factory::create();
        $slug = $faker->word();

        $taxonomy = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::with('term')->first();

        $response = $this->actingAs($user)
                        ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@create').'?code='.encrypt($taxonomy->getKey()))
                        ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@store'), [
                            'term' => ['name' => $taxonomy->term->name, 'slug' => $slug],
                            'taxonomy' => ['description' => $faker->text, 'taxonomy' => 'tag'],
                            $taxonomy->getKeyName() => encrypt($taxonomy->getKey()),
                            '_method' => 'PUT'
                        ])
                        ->assertStatus(302)
                        ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                        ->assertSessionHas('global_message.status', 200)
                        ->assertSessionHasNoErrors(); //Return Valid, Data Complete
    }

    public function testDeleteDataTag()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@destroy'), [
                        '_method' => 'DELETE'
                    ]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \App\User::find(1);

        $taxonomy = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::first();

        $response = $this->actingAs($user)
                        ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                        ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@destroy'), [
                            $taxonomy->getKeyName() => encrypt($taxonomy->getKey()),
                            '_method' => 'DELETE'
                        ])
                        ->assertStatus(302)
                        ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                        ->assertSessionHas('global_message.status', 200);

        $this->assertDatabaseMissing(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getTableName(), [$taxonomy->getKeyName() => $taxonomy->getKey()]);
    }
}
