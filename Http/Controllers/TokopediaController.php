<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;

use StorageService;
use MarketPlace;

class TokopediaController extends Controller
{
    public function getData(Request $request)
    {
        $request->merge([
            'shop_id' => $request->store_name
        ]);

        $result = MarketPlace::driver('tokopedia')->item->getItemsList($request->input());

        $tmp = collect($result->data->list);

        $data = $tmp->map(function($item, $key) use ($request) {
            $item->store = $request->store_name;
            $item->slug = $this->slugify($item->product_name);
            $item->url = 'https://www.tokopedia.com/'.$request->input('store_name').'/'.$item->slug;
            return $item;
        });

        $builder = PostMeta::where('meta_key', ProductMeta::TOKPED_SUPPLIER)
                            ->where('meta_value', 'LIKE', '%\"merchant\":\"'.$request->store_name.'\"%');

        $tmp = $builder->pluck('meta_value');

        $tmp = collect($tmp);

        $slug = $tmp->map(function($item, $key){
          return $item['slug'];
        });

        $filter = $data->whereNotIn('slug', $slug)->toArray();

        return response()
            ->json(['paging' => $result->data->paging, 'list' => $filter]);
    }

    public function store(Request $request)
    {
      $this->validate($request, [
        'product_image.*' => 'url'
      ]);
      
      $text = nl2br($request->input('post.post_content'));

      $post = $request->input('post');
      $post['post_content'] = $text;

      $request->merge([
        'post' => $post
      ]);

      $response = resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\ProductRepository::class)->save($request);
      $post = $response->data;

      if($request->has('product_image'))
      {
        foreach ($request->product_image as $key => $value) {

          if($key == 0)
          {
            $path = StorageService::putImageUrl('scrapping/'.$post->post_slug, $value, true);
            $cover_image['caption'] = null;
            $cover_image['file'] = $path->file;
            $cover_image['small'] = $path->small;
            $cover_image['thumb'] = $path->thumb;
            $cover_image['medium'] = $path->medium;

            resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\ProductRepository::class)->saveImage($post, $cover_image);
          }
          else
          {
            $path = StorageService::putImageUrl('scrapping/'.$post->post_slug, $value);
            $data[] = ['photo' => '/'.$path->file];

            PostMeta::unguard();
            PostMeta::updateOrCreate(
                ['meta_key' => 'gallery', Post::FOREIGN_KEY => $post->getKey()],
                ['meta_value' => $data]
            );
            PostMeta::reguard();
          }
        }
      }

      return response()->json([
        'status' => $response->status
      ]); 
    }

    protected static function slugify($text)
    {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }
}
