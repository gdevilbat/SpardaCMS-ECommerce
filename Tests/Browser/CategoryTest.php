<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryTest extends DuskTestCase
{
    use DatabaseMigrations, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testCreateCategory()
    {
        $user = \App\Models\User::find(1);
        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                    ->assertSee('Master Data of Categories')
                    ->clickLink('Add New Category')
                    ->waitForText('Category Form')
                    ->AssertSee('Category Form')
                    ->type('term[name]', $faker->word)
                    ->type('term[slug]', $faker->word)
                    ->type('taxonomy[description]', $faker->text)
                    ->script('document.getElementsByName("taxonomy[parent_id]")[0].selectedIndex = 1');

            $browser->press('Submit')
                    ->waitForText('Master Data of Categories')
                    ->assertSee('Successfully Add Category!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testEditCategory()
    {
        $user = \App\Models\User::find(1);

        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                    ->assertSee('Master Data of Categories')
                    ->waitForText('Actions')
                    ->clickLink('Actions')
                    ->clickLink('Edit')
                    ->AssertSee('Category Form')
                    ->type('term[name]', $faker->word)
                    ->type('term[slug]', $faker->word)
                    ->type('taxonomy[description]', $faker->text)
                    ->script('document.getElementsByName("taxonomy[parent_id]")[0].selectedIndex = 1');

            $browser ->press('Submit')
                    ->waitForText('Master Data of Categories')
                    ->assertSee('Successfully Update Category!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testDeleteCategory()
    {
        $user = \App\Models\User::find(1);

        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index'))
                    ->assertSee('Master Data of Categories')
                    ->waitForText('Actions')
                    ->clickLink('Actions')
                    ->clickLink('Delete')
                    ->waitForText('Delete Confirmation')
                    ->press('Delete')
                    ->waitForText('Master Data of Categories')
                    ->assertSee('Successfully Delete Taxonomy!');
        });
    }
}
