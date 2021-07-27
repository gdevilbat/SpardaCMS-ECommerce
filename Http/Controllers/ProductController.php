<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;


use Gdevilbat\SpardaCMS\Modules\Post\Foundation\AbstractPost;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\Repository;

use Auth;
use View;

class ProductController extends AbstractPost
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Repositories\ProductRepository $post_repository)
    {
        parent::__construct($post_repository);
        $this->post_m = new Product;
        $this->product_meta_m = new ProductMeta;
        $this->product_meta_repository = new Repository(new ProductMeta, resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class));
    }

    public function getColumnOrder()
    {
        return ['', '', Product::getPrimaryKey(), 'post_title', 'author', 'categories', 'tags','comment', 'post_status', 'product_price', 'product_sale', 'tokopedia_slug', 'tokopedia_source', 'availability', 'created_at'];
    }

    public function getQuerybuilder($column, $dir)
    {
        $query = parent::getQuerybuilder($column, $dir);

        $query = $query->leftJoin($this->product_meta_m::getTableName(), $this->post_m->getTableName().'.'.$this->post_m->getPrimaryKey(), '=', $this->product_meta_m::getTableName().'.product_id')
                        ->select([$this->post_m->getTableName().'.*' , $this->product_meta_m::getTableName().'.product_price', $this->product_meta_m::getTableName().'.product_sale']);

        return $query;
    }

    public function parsingDataTable($posts)
    {
        /*=========================================
        =            Parsing Datatable            =
        =========================================*/
            
            $data = array();
            $i = 0;
            foreach ($posts as $key_post => $post) 
            {
                $data[$i][] = '';
                $data[$i][] = '<input class="data-checklist" type="checkbox" data-index="'.$post->getKey().'">';
                $data[$i][] = $post->getKey();
                $data[$i][] = $post->post_title;

                $data[$i][] = $post->author->name;

                $categories = $post->taxonomies->where('taxonomy', $this->post_repository->getCategory());
                if($categories->count() > 0)
                {
                    $data[$i][] = '';
                    foreach ($categories as $key => $category) 
                    {
                        $data[$i][count($data[$i]) - 1] .= $this->getCategoryHtmlTag($this->getPostCategory($category)).'</br>';
                    }
                }
                else
                {
                    $data[$i][] = '-';
                }

                $tags = $post->taxonomies->where('taxonomy', 'tag');
                if($tags->count() > 0)
                {
                    $data[$i][] = '';
                    foreach ($tags as $key => $tag) 
                    {
                        $data[$i][count($data[$i]) - 1] .= '<span class="badge badge-danger mx-1">'.$tag->term->name.'</span>';
                    }
                }
                else
                {
                    $data[$i][] = '-';
                }

                $data[$i][] = '';

                if($post->post_status_bool)
                {
                    $data[$i][] = '<a href="#" class="btn btn-success p-1">'.$post->post_status.'</a>';
                }
                else
                {
                    $data[$i][] = '<a href="#" class="btn btn-warning p-1">'.$post->post_status.'</a>';
                }

                if(!empty($post->productMeta))
                {
                    $data[$i][] = '<span id="'.($post->productMeta->product_sale == 0 ? "web-price-".$post->getKey() : "#").'" data-price="'.$post->productMeta->product_price.'" data-index="'.$post->getKey().'">'.$post->productMeta->product_price.'</span>';
                    $data[$i][] = '<span id="'.($post->productMeta->product_sale > 0 ? "web-price-".$post->getKey() : "#").'" data-price="'.$post->productMeta->product_sale.'" data-index="'.$post->getKey().'">'.$post->productMeta->product_sale.'</span>';
                }
                else
                {
                    $data[$i][] = '-';
                    $data[$i][] = '-';
                }

                $tokopedia_supplier = $post->meta->getMetaData(ProductMeta::TOKPED_SUPPLIER);

                if(!empty($tokopedia_supplier) && $tokopedia_supplier->slug != '')
                {
                    $data[$i][] = '<a href="https://tokopedia.com/'.$tokopedia_supplier->merchant.'/'.$tokopedia_supplier->slug.'" target="_blank">'.'<span data-index='.$post->getKey().' class="scrapping-supplier" id="scrapping-supplier-'.$post->getKey().'" data-url="https://tokopedia.com/'.$tokopedia_supplier->merchant.'/'.$tokopedia_supplier->slug.'" data-merchant="'.$tokopedia_supplier->merchant.'" data-slug="'.$tokopedia_supplier->slug.'"></span> Tokopedia'.'</a>';
                }
                else
                {
                    $data[$i][] = '-';
                }

                $tokopedia_store = $post->meta->getMetaData(ProductMeta::TOKPED_STORE);
                $shopee_store = $post->meta->getMetaData(ProductMeta::SHOPEE_STORE);
                $lazada_store = $post->meta->getMetaData(ProductMeta::LAZADA_STORE);

                $data[$i][] = $this->getStoreLink([
                                    'post' => $post,
                                    'tokopedia_store' => $tokopedia_store,
                                    'shopee_store' => $shopee_store,
                                    'lazada_store' => $lazada_store,
                                ]);

                if($post->productMeta->availability == 'in stock')
                {
                    if(empty($post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT)))
                    {
                        $data[$i][] = '<span class="badge badge-info">'.$post->productMeta->availability.'</span>';
                    }
                    else
                    {
                        $data[$i][] = '<span class="badge badge-info">'.$post->productMeta->availability.'</span><br/><span class="badge badge-danger">is variant</span>';
                    }
                }
                elseif($post->productMeta->availability == 'out of stock')
                {
                    if(empty($post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT)))
                    {
                        $data[$i][] = '<span class="badge badge-dark">'.$post->productMeta->availability.'</span>';
                    }
                    else
                    {
                        $data[$i][] = '<span class="badge badge-dark">'.$post->productMeta->availability.'</span><br/><span class="badge badge-danger">is variant</span>';
                    }
                }
                else
                {
                    $data[$i][] = $post->productMeta->availability;
                }

                $data[$i][] = $post->created_at->toDateTimeString();
                $data[$i][] = $this->getActionTable($post);
                $i++;
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    public function getStoreLink(array $data)
    {
        $view = View::make($this->post_repository->getModule().'::admin.'.$this->data['theme_cms']->value.'.partials'.'.store_link', $data);

        $html = $view->render();
       
       return $html;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view($this->post_repository->getModule().'::show');
    }


    public function apiProductDetail(Request $request)
    {
        $request->validate([
            'id_posts' => 'required',
        ]);

        $post = $this->post_repository->with(['productMeta', 'postMeta'])->findOrFail(decrypt($request->input('id_posts')));

        $photo = [];

        if(!empty($post->postMeta->where('meta_key', 'cover_image')->first()) && $post->postMeta->where('meta_key', 'cover_image')->first()->meta_value['file'] != null)
            $photo[] = generate_storage_url($post->postMeta->where('meta_key', 'cover_image')->first()->meta_value['file']);

        if(!empty($post->postMeta->where('meta_key', 'gallery')->first()))
        {
            foreach ($post->postMeta->where('meta_key', 'gallery')->first()->meta_value as $key => $value) {
               $photo[] = generate_storage_url($value['photo']);
            }
        }

        $post->image_url = $photo;
        $post->post_content = html_entity_decode(strip_tags($post->post_content));
        $post->condition = $post->productMeta->condition;
        $post->product_weight = $post->meta->getMetaData(ProductMeta::PRODUCT_WEIGHT);
        $post->product_stock = $post->productMeta->product_stock;
        $post->product_variant = !empty($post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT)) ? $post->meta->getMetaData(ProductMeta::PRODUCT_VARIANT)->get() : [];
        $post->price = $post->productMeta->product_price;
        $post->sale = $post->productMeta->product_sale;

        return $post;

    }
}
