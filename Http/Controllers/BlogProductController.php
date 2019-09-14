<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Blog\Foundation\AbstractBlog;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product;

use Auth;

class BlogProductController extends AbstractBlog
{
    public function __construct()
    {
        parent::__construct();
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


        $this->data['post_categories'] = $this->data['post']->load(['taxonomies' => function($query){
                                            $query->where('taxonomy', 'product-category');
                                        }, 'taxonomies.term'])->taxonomies;

        $this->data['post_tags'] = $this->data['post']->load(['taxonomies' => function($query){
                                            $query->where('taxonomy', 'tag');
                                        }, 'taxonomies.term'])->taxonomies;


        /*===========================================
        =            Recent Suggest Product            =
        ===========================================*/
        
            $query = $this->post_m->with('postMeta')
                                                ->whereHas('productMeta', function($query){
                                                    $query->where('availability', 'in stock');
                                                })
                                                ->where(['post_type' =>  $this->getPostType()])
                                                ->where(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getPrimaryKey(), '!=', $this->data['post']->getKey())
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
                                                    $query->where('availability', 'in stock');
                                                })
                                                ->where(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getPrimaryKey(), '!=', $this->data['post']->getKey())
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
                                                    $query->where('availability', 'in stock');
                                                })
                                                ->where(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\Product::getPrimaryKey(), '!=', $this->data['post']->getKey())
                                                ->inRandomOrder()
                                                ->limit(3);

            if(!Auth::check())
            {
                $query = $query->where('post_status',  'publish');
            }

            $this->data['recomended_posts'] = $query->get();
        
        /*=====  End of Recomended Product  ======*/


        $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.templates.parent';

        if(file_exists(module_asset_path('appearance:resources/views/general/'.$this->data['theme_public']->value.'/content/'.$this->data['post']->post_type.'-'.$this->data['post']->getKey().'.blade.php')))
        {
            $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.content.'.$this->data['post']->post_type.'-'.$this->data['post']->getKey();
        }
        elseif(file_exists(module_asset_path('appearance:resources/views/general/'.$this->data['theme_public']->value.'/content/'.$this->data['post']->post_type.'-'.$this->data['post']->post_slug.'.blade.php')))
        {
            $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.content.'.$this->data['post']->post_type.'-'.$this->data['post']->post_slug;
        }
        elseif(file_exists(module_asset_path('appearance:resources/views/general/'.$this->data['theme_public']->value.'/content/'.$this->data['post']->post_slug.'.blade.php')))
        {
            $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.content.'.$this->data['post']->post_slug;
        }
        elseif(file_exists(module_asset_path('appearance:resources/views/general/'.$this->data['theme_public']->value.'/content/'.$this->data['post']->post_type.'.blade.php')))
        {
            $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.content.'.$this->data['post']->post_type;
        }
        else
        {
            $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.content.post';
        }

        return response()
            ->view($path_view, $this->data);

    }
}
