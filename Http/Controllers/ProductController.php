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

class ProductController extends AbstractPost
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->post_m = new Product;
        $this->post_repository = new Repository(new Product);
        $this->product_meta_m = new ProductMeta;
        $this->product_meta_repository = new Repository(new ProductMeta);

        $this->module = 'ecommerce';
        $this->post_type = 'product';
    }

    public function getColumnOrder()
    {
        return [Product::getPrimaryKey(), 'post_title', 'product_price', 'product_sale', 'author', 'categories', 'tags','comment', 'status','created_at'];
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
                if(Auth::user()->can('read-'.$this->getModule(), $post))
                {
                    $data[$i][] = $post->getKey();
                    $data[$i][] = $post->post_title;
                    $data[$i][] = $post->productMeta->product_price;
                    $data[$i][] = $post->productMeta->product_sale;
                    $data[$i][] = $post->author->name;

                    $categories = $post->taxonomies->where('taxonomy', $this->getCategory());
                    if($categories->count() > 0)
                    {
                        $data[$i][] = '';
                        foreach ($categories as $key => $category) 
                        {
                            $data[$i][count($data[$i]) - 1] .= '<span class="badge badge-danger mx-1">'.$category->term->name.'</span>';
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
                        $data[$i][] = '<a href="#" class="btn btn-success p-1">'.$post->post_status.'</a>';;
                    }
                    else
                    {
                        $data[$i][] = '<a href="#" class="btn btn-warning p-1">'.$post->post_status.'</a>';;
                    }

                    $data[$i][] = $post->created_at->toDateTimeString();
                    $data[$i][] = $this->getActionTable($post);
                    $i++;
                }
            }

            return $data;
        
        /*=====  End of Parsing Datatable  ======*/
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view($this->getModule().'::show');
    }

    public function getCategory()
    {
        return 'product-category';
    }

    public function productMetaStore(Request $request, Product $post)
    {
        if($request->isMethod('POST'))
        {
            $product_meta = new $this->product_meta_m;
            $product_meta[\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey()] = $post->getKey();
        }
        else
        {
            $product_meta = $this->product_meta_m->find(decrypt($request->input(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getPrimaryKey())));
            if(empty($product_meta))
            {
                $product_meta = new $this->product_meta_m;
                $product_meta[\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::getPrimaryKey()] = $post->getKey();
            }
        }

        foreach ($request->input('product_meta') as $key => $value) 
        {
            $product_meta->$key = $value;
        }

        $product_meta->save();
    }

    public function getTag()
    {
        return 'tag';
    }

    public function validatePost(Request $request)
    {
        $validator = parent::validatePost($request);

        $validator->addRules([
                'meta.gallery.*.photo' => 'required',
                'product_meta.product_price' => 'required|max:11',
        ]);

        if(!empty($request->input('product_meta.product_sale')))
        {
            $validator->addRules([
                    'product_meta.product_sale' => 'max:11'
            ]);
        }

        return $validator;
    }
}
