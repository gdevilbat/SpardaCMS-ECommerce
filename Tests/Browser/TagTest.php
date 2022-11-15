<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TagTest extends DuskTestCase
{
    use DatabaseMigrations, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testCreateTag()
    {
        $user = \App\Models\User::find(1);
        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                    ->assertSee('Master Data of Tags')
                    ->clickLink('Add New Tag')
                    ->waitForText('Tag Form')
                    ->AssertSee('Tag Form')
                    ->type('term[name]', $faker->word)
                    ->type('term[slug]', $faker->word)
                    ->type('taxonomy[description]', $faker->text)
                    ->press('Submit')
                    ->waitForText('Master Data of Tags')
                    ->assertSee('Successfully Add Tag!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testEditTag()
    {
        $user = \App\Models\User::find(1);
        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                    ->assertSee('Master Data of Tags')
                    ->waitForText('Actions')
                    ->clickLink('Actions')
                    ->clickLink('Edit')
                    ->AssertSee('Tag Form')
                    ->type('term[name]', $faker->word)
                    ->type('term[slug]', $faker->word)
                    ->type('taxonomy[description]', $faker->text);

            $browser->press('Submit')
                    ->waitForText('Master Data of Tags')
                    ->assertSee('Successfully Update Tag!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testDeleteTag()
    {
        $user = \App\Models\User::find(1);

        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index'))
                    ->assertSee('Master Data of Tags')
                    ->waitForText('Actions')
                    ->clickLink('Actions')
                    ->clickLink('Delete')
                    ->waitForText('Delete Confirmation')
                    ->press('Delete')
                    ->waitForText('Master Data of Tags')
                    ->assertSee('Successfully Delete Taxonomy!');
        });
    }
}
