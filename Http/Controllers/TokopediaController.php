<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;

use StorageService;

class TokopediaController extends Controller
{
    public function getData(Request $request)
    {
        $this->validate($request, [
          'store_name' => 'required',
          'page' => 'required|min:1'
        ]);

        $page = $request->input('page') ?: 1;
        $limit = $request->input('limit') ?: 80;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://gql.tokopedia.com/",
          CURLOPT_ENCODING => "",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS =>"[{\"operationName\":\"ShopInfoCore\",\"variables\":{\"id\":0,\"domain\":\"".$request->input('store_name')."\"},\"query\":\"query ShopInfoCore(\$id: Int!, \$domain: String) {\\n  shopInfoByID(input: {shopIDs: [\$id], fields: [\\\"active_product\\\", \\\"address\\\", \\\"allow_manage\\\", \\\"assets\\\", \\\"core\\\", \\\"closed_info\\\", \\\"create_info\\\", \\\"favorite\\\", \\\"location\\\", \\\"status\\\", \\\"is_open\\\", \\\"other-goldos\\\", \\\"shipment\\\", \\\"shopstats\\\", \\\"shop-snippet\\\", \\\"other-shiploc\\\", \\\"shopHomeType\\\"], domain: \$domain, source: \\\"shoppage\\\"}) {\\n    result {\\n      shopCore {\\n        description\\n        domain\\n        shopID\\n        name\\n        tagLine\\n        defaultSort\\n        __typename\\n      }\\n      createInfo {\\n        openSince\\n        __typename\\n      }\\n      favoriteData {\\n        totalFavorite\\n        alreadyFavorited\\n        __typename\\n      }\\n      activeProduct\\n      shopAssets {\\n        avatar\\n        cover\\n        __typename\\n      }\\n      location\\n      isAllowManage\\n      isOpen\\n      shopHomeType\\n      address {\\n        name\\n        id\\n        email\\n        phone\\n        area\\n        districtName\\n        __typename\\n      }\\n      shipmentInfo {\\n        isAvailable\\n        image\\n        name\\n        product {\\n          isAvailable\\n          productName\\n          uiHidden\\n          __typename\\n        }\\n        __typename\\n      }\\n      shippingLoc {\\n        districtName\\n        cityName\\n        __typename\\n      }\\n      shopStats {\\n        productSold\\n        totalTxSuccess\\n        totalShowcase\\n        __typename\\n      }\\n      statusInfo {\\n        shopStatus\\n        statusMessage\\n        statusTitle\\n        __typename\\n      }\\n      closedInfo {\\n        closedNote\\n        until\\n        reason\\n        __typename\\n      }\\n      bbInfo {\\n        bbName\\n        bbDesc\\n        bbNameEN\\n        bbDescEN\\n        __typename\\n      }\\n      goldOS {\\n        isGold\\n        isGoldBadge\\n        isOfficial\\n        badge\\n        __typename\\n      }\\n      shopSnippetURL\\n      customSEO {\\n        title\\n        description\\n        bottomContent\\n        __typename\\n      }\\n      __typename\\n    }\\n    error {\\n      message\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"}]",
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $id = json_decode($response)[0]->data->shopInfoByID->result[0]->shopCore->shopID;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://ace.tokopedia.com/v1/web-service/shop/get_shop_product?etalase=etalase&order_by=9&page=".$page."&per_page=".$limit."&shop_id=".$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            'cache-control: max-age=0',
            'accept-language: en-US,en;q=0.9',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response);

        $tmp = collect($result->data->list);

        $data = $tmp->map(function($item, $key) use ($request) {
            $item->store = $this->slugify($item->shop_name);
            $item->slug = $this->slugify($item->product_name);
            $item->url = 'https://www.tokopedia.com/'.$request->input('store_name').'/'.$item->slug;
            return $item;
        });

        $store = json_encode($request->input('store_name'));

        /*$builder = PostMeta::whereExists(function($query) use ($store){
                        $query->select(\DB::raw(1))
                                ->from(PostMeta::getTableName())
                               ->where('meta_key', 'tokopedia_supplier')
                               ->where('meta_value', $store);
                    })
                    ->where('meta_key', 'tokopedia_source');

        $slug = $builder->pluck('meta_value');*/

        $builder = PostMeta::from(PostMeta::getTableName().' as table_1')
                            ->join(PostMeta::getTableName(), function($join) use ($store){
                                $join->on('table_1.'.Post::FOREIGN_KEY, '=', PostMeta::getTableName().'.'.Post::FOREIGN_KEY)
                                    ->where('table_1.meta_key', 'tokopedia_supplier')
                                   ->where('table_1.meta_value', $store);
                              })
                            ->where(PostMeta::getTableName().'.meta_key', 'tokopedia_source');

        $tmp = $builder->pluck(PostMeta::getTableName().'.meta_value');

        $tmp = collect($tmp);

        $slug = $tmp->map(function($item, $key){
          return trim($item, '"');
        });

        $filter = $data->whereNotIn('slug', $slug)->toArray();

        return response()
            ->json(['paging' => $result->data->paging, 'list' => $filter]);
    }

    public function store(Request $request)
    {
      $text = nl2br($request->input('post.post_content'));

      $post = $request->input('post');
      $post['post_content'] = $text;

      $request->merge([
        'post' => $post
      ]);

      $response = resolve(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\ProductRepository::class)->save($request);
      $post = $response->data;

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
