<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductTest extends DuskTestCase
{
    use DatabaseMigrations, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testCreateProduct()
    {
        $user = \App\User::find(1);
        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                    ->assertSee('Master Data of Product')
                    ->clickLink('Add New Product')
                    ->waitForText('Product Form')
                    ->AssertSee('Product Form')
                    ->type('post[post_title]', $faker->name)
                    ->type('post[post_slug]', $faker->name)
                    ->type('product_meta[product_sale]', $faker->randomNumber(6))
                    ->type('meta[meta_title]', $faker->name)
                    ->type('meta[meta_description]', $faker->text);

            $browser->script('document.getElementsByName("post[post_content]")[0].value = "'.$faker->text.'"');
            $browser->script('document.getElementsByName("post[post_status]")[0].checked = true');
            $browser->script('document.getElementsByName("post[comment_status]")[0].checked = true');
            //$browser->script('document.getElementsByName("post[post_parent]")[0].selectedIndex = 1'); Disable For A While
            $browser->script('document.getElementsByName("product_meta[product_price]")[0].value = "'.$faker->randomNumber(6).'"');
            $browser->script('document.getElementsByName("product_meta[availability]")[0].checked = true');
            $browser->script('document.getElementsByName("product_meta[condition]")[0].checked = true');
            $browser->script('document.getElementsByName("taxonomy[category][]")[0].selectedIndex = 1');
            $browser->script('document.getElementsByName("taxonomy[tag][]")[0].selectedIndex = 0');
            $browser->script('document.getElementsByName("meta[meta_keyword]")[0].value = "'.$faker->name.'"');
            $browser->script('document.querySelectorAll("[type=submit]")[0].click()');

            $browser->waitForText('Master Data of Product')
                    ->assertSee('Successfully Add Product!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testEditProduct()
    {
        $user = \App\User::find(1);
        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                    ->assertSee('Master Data of Product')
                    ->waitForText('Actions')
                    ->clickLink('Actions')
                    ->clickLink('Edit')
                    ->AssertSee('Product Form')
                    ->type('post[post_title]', $faker->name)
                    ->type('post[post_slug]', $faker->name)
                    ->type('product_meta[product_sale]', $faker->randomNumber(6))
                    ->type('meta[meta_title]', $faker->name)
                    ->type('meta[meta_description]', $faker->text);

            $browser->script('document.getElementsByName("post[post_content]")[0].value = "'.$faker->text.'"');
            $browser->script('document.getElementsByName("post[post_status]")[0].checked = true');
            $browser->script('document.getElementsByName("post[comment_status]")[0].checked = true');
            //$browser->script('document.getElementsByName("post[post_parent]")[0].selectedIndex = 1'); Disable For A While
            $browser->script('document.getElementsByName("product_meta[product_price]")[0].value = "'.$faker->randomNumber(6).'"');
            $browser->script('document.getElementsByName("product_meta[availability]")[0].checked = true');
            $browser->script('document.getElementsByName("product_meta[condition]")[0].checked = true');
            $browser->script('document.getElementsByName("taxonomy[category][]")[0].selectedIndex = 1');
            $browser->script('document.getElementsByName("taxonomy[tag][]")[0].selectedIndex = 0');
            $browser->script('document.getElementsByName("meta[meta_keyword]")[0].value = "'.$faker->name.'"');
            $browser->script('document.querySelectorAll("[type=submit]")[0].click()');

            $browser->waitForText('Master Data of Product')
                    ->assertSee('Successfully Update Product!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testDeleteProduct()
    {
        $user = \App\User::find(1);

        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index'))
                    ->assertSee('Master Data of Product')
                    ->waitForText('Actions')
                    ->clickLink('Actions')
                    ->clickLink('Delete')
                    ->waitForText('Delete Confirmation')
                    ->press('Delete')
                    ->waitForText('Master Data of Product')
                    ->assertSee('Successfully Delete Product!');
        });
    }

}
