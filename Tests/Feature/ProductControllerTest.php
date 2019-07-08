<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReadPost()
    {
        $response = $this->get(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); // Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                         ->json('GET',action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@serviceMaster'))
                         ->assertSuccessful()
                         ->assertJsonStructure(['data', 'draw', 'recordsTotal', 'recordsFiltered']); // Return Valid user Login
    }

    public function testCreateDataPost()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@store'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@create'))
                         ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@store'))
                         ->assertStatus(302)
                         ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@create'))
                         ->assertSessionHasErrors(); //Return Not Valid, Data Not Complete

        $faker = \Faker\Factory::create();
        $name = $faker->word;
        $slug = $faker->word;

        $category = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::where(['taxonomy' => 'category'])->first();
        $tag = \Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::where(['taxonomy' => 'tag'])->first();

        $post = \Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::where('post_type', 'post')->first();

        $response = $this->actingAs($user)
                         ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@create'))
                         ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@store'), [
                                'post' => ['post_title' => $name, 'post_slug' => $slug, 'post_content' => $faker->text, 'post_parent' => $post->getKey()],
                                'taxonomy' => ['category' => [$category->getKey()], 'tag' => [$tag->getKey()]]
                            ])
                         ->assertStatus(302)
                         ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                         ->assertSessionHas('global_message.status', 200)
                         ->assertSessionHasNoErrors(); //Return Valid, Data Complete

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getTableName(), ['post_slug' => $slug, 'post_parent' => $post->getKey()]);

        $post = \Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::where(['post_slug' => $slug])->first();

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Post\Entities\TermRelationship::getTableName(), [
            'object_id' => $post->getKey(), 
            'term_taxonomy_id' => $category->getKey()
        ]);

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Post\Entities\TermRelationship::getTableName(), [
            'object_id' => $post->getKey(), 
            'term_taxonomy_id' => $tag->getKey()
        ]);
    }

    public function testUpdateDataPost()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@store'), [
                        '_method' => 'PUT'
                    ]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::with('role')->find(1);

        $post = \Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::where('post_type', 'post')->first();

        $response = $this->actingAs($user)
                        ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@create').'?code='.encrypt($post->getKey()))
                        ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@store'), [
                            'post' => ['post_title' => $post->post_title, 'post_slug' => $post->post_slug, 'post_content' => $post->post_content, 'post_parent' => $post->getKey()],
                            $post->getKeyName() => encrypt($post->getKey()),
                            '_method' => 'PUT'
                        ])
                        ->assertStatus(302)
                        ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                        ->assertSessionHas('global_message.status', 200)
                        ->assertSessionHasNoErrors(); //Return Valid, Data Complete
    }

    public function testDeleteDataPost()
    {
        $response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@destroy'), [
                        '_method' => 'DELETE'
                    ]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \App\User::find(1);

        $post = \Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::where('post_type', 'post')->first();

        $response = $this->actingAs($user)
                        ->from(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                        ->post(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@destroy'), [
                            $post->getKeyName() => encrypt($post->getKey()),
                            '_method' => 'DELETE'
                        ])
                        ->assertStatus(302)
                        ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                        ->assertSessionHas('global_message.status', 200);

        $this->assertDatabaseMissing(\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getTableName(), [$post->getKeyName() => $post->getKey()]);
    }
}
