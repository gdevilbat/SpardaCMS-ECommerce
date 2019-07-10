<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gdevilbat\SpardaCMS\Modules\Blog\Foundation\AbstractBlog;

class BlogProductController extends AbstractBlog
{
    public function __construct()
    {
        parent::__construct();
        $this->post_type = 'product';
    }

    public function show($slug)
    {
        $this->data['post'] = $this->post_m::with('postMeta', 'author')
                                            ->where(['post_slug' => $slug, 'post_type' => $this->getPostType(), 'post_status' => 'publish'])
                                            ->firstOrFail();

        $this->data['recent_posts'] = $this->post_m->with('postMeta')
                                            ->where(['post_type' =>  $this->getPostType(), 'post_status' => 'publish'])
                                            ->where(\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getPrimaryKey(), '!=', $this->data['post']->getKey())
                                            ->latest('created_at')
                                            ->limit(5)
                                            ->get();

        $this->data['post_categories'] = $this->data['post']->load(['taxonomies' => function($query){
                                            $query->where('taxonomy', 'category');
                                        }, 'taxonomies.term'])->taxonomies;

        $this->data['post_tags'] = $this->data['post']->load(['taxonomies' => function($query){
                                            $query->where('taxonomy', 'tag');
                                        }, 'taxonomies.term'])->taxonomies;

        $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.templates.parent';

        if(empty($this->data['post']))
        {
            return response()
                ->view($path_view, $this->data, 404);
        }

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
        else
        {
            $path_view = 'appearance::general.'.$this->data['theme_public']->value.'.content.post';
        }

        return response()
            ->view($path_view, $this->data);

    }
}
