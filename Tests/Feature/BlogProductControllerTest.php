<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlogProductControllerTest extends TestCase
{
	use RefreshDatabase, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;

    public function testPage()
    {
    	$post = \Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::where(['post_type' => 'product'])->first();
    	$post->post_status = 'publish';
    	$post->save();

    	$response = $this->get(url('product/'.$post->post_slug));

        $response->assertSuccessful();
    }
}
