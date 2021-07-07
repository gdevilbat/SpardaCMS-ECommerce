<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Blog\Foundation\AbstractBlog;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;

use Auth;

class BlogProductController extends AbstractBlog
{
    public function __construct(\Gdevilbat\SpardaCMS\Modules\Post\Repositories\PostRepository $post_repository)
    {
        parent::__construct($post_repository);
        $this->post_type = 'product';
        $this->post_m = new Product;
    }

    public function show($slug)
    {
        /*===============================
        =            Product            =
        ===============================*/
            
            $query = $this->post_m::with('postMeta', 'author', 'productMeta')
                                                ->where(['post_slug' => $slug, 'post_type' => $this->getPostType()]);

            if(!Auth::check())
            {
                $query = $query->where('post_status',  'publish');
            }

            $this->data['post'] = $query->first();

            if(empty($this->data['post']))
            {
                return $this->throwError(404);
            }
        
        /*=====  End of Product  ======*/


        $this->data['post_categories'] = $this->getPostCategory($this->data['post']);

        $this->data['post_tags'] = $this->getPostTag($this->data['post']);


        /*===========================================
        =            Recent Suggest Product            =
        ===========================================*/
        
            $query = $this->post_m->with('postMeta')
                                                ->whereHas('productMeta', function($query){
                                                    $query->where('product_stock', '>', 0);
                                                })
                                                ->where(['post_type' =>  $this->getPostType()])
                                                ->where(Product::getPrimaryKey(), '!=', $this->data['post']->getKey())
                                                ->latest('created_at')
                                                ->limit(3);

            if(!Auth::check())
            {
                $query = $query->where('post_status',  'publish');
            }

            $this->data['recent_posts'] = $query->get();
        
        /*=====  End of Recent Suggest Product  ======*/
        

        /*===========================================
        =            Related Product            =
        ===========================================*/
        
            $query = $this->buildPostByTaxonomy($this->data['post_categories']->first())
                                                ->with('postMeta')
                                                ->whereHas('productMeta', function($query){
                                                    $query->where('product_stock', '>', 0);
                                                })
                                                ->where(Product::getPrimaryKey(), '!=', $this->data['post']->getKey())
                                                ->inRandomOrder()
                                                ->limit(3);

            $this->data['related_posts'] = $query->get();
        
        /*=====  End of Related Product  ======*/


        /*===========================================
        =            Recomended Product            =
        ===========================================*/
        
            $query = $this->post_m->with('postMeta')
                                                ->where(['post_type' =>  $this->getPostType()])
                                                ->whereHas('productMeta', function($query){
                                                    $query->where('product_stock', '>', 0);
                                                })
                                                ->where(Product::getPrimaryKey(), '!=', $this->data['post']->getKey())
                                                ->inRandomOrder()
                                                ->limit(3);

            if(!Auth::check())
            {
                $query = $query->where('post_status',  'publish');
            }

            $this->data['recomended_posts'] = $query->get();
        
        /*=====  End of Recomended Product  ======*/


        return response()
            ->view($this->getPathView(), $this->data);

    }

    public function getPostData($taxonomy)
    {
        $query_1 = $this->buildPostByTaxonomy($taxonomy)
                        ->join(ProductMeta::getTableName(), Product::getTableName().'.'.Product::getPrimaryKey(), '=', ProductMeta::getTableName().'.product_id')
                        ->orderByRaw('if('.ProductMeta::getTableWithPrefix().'.product_stock > 0, "'.ProductMeta::STAT_IN_STOCK.'", "'.ProductMeta::STAT_OUT_STOCK.'") ASC, '.Product::getTableWithPrefix().'.created_at DESC');

        return $query_1;
    }

    final protected function getCategoryType()
    {
        return 'product-category';
    }

    final protected function getTagType()
    {
        return 'tag';
    }
}
