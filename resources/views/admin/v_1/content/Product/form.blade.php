@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'))}}
    {{Html::style(module_asset_url('core:assets/metronic-v5/global/plugins/typeahead/typeaheadjs.css'))}}
@endsection

@section('title_dashboard', 'Product')

@section('breadcrumb')
        <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
            <li class="m-nav__item m-nav__item--home">
                <a href="#" class="m-nav__link m-nav__link--icon">
                    <i class="m-nav__link-icon la la-home"></i>
                </a>
            </li>
            <li class="m-nav__separator">-</li>
            <li class="m-nav__item">
                <a href="" class="m-nav__link">
                    <span class="m-nav__link-text">Home</span>
                </a>
            </li>
            <li class="m-nav__separator">-</li>
            <li class="m-nav__item">
                <a href="" class="m-nav__link">
                    <span class="m-nav__link-text">Product</span>
                </a>
            </li>
        </ul>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <form class="m-form m-form--fit m-form--label-align-right" action="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@store')}}" method="post" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="row">
                <div class="col-md-8">
                    <div class="m-portlet m-portlet--last m-portlet--head-lg m-portlet--responsive-mobile" id="main_portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-wrapper">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            Product Form
                                        </h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools">
                                    <div class="row justify-content-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--begin::Form-->
                            <div class="m-portlet__body">
                                <div class="col-md-9 offset-md-3">
                                    @if (!empty(session('global_message')))
                                        <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }}">
                                            {{session('global_message')['message']}}
                                        </div>
                                    @endif
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#" data-target="#content">Content</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#" data-target="#variant-ecommerce">Variant</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#" data-target="#marketplace">Ecommerce</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#" data-target="#gallery">Gallery</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="content" role="tabpanel">
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Title<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control m-input slugify" data-target="slug" placeholder="Product Title" name="post[post_title]" value="{{old('post.post_title') ? old('post.post_title') : (!empty($post) ? $post->post_title : '')}}">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Slug<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control m-input" id="slug" placeholder="Product Slug" name="post[post_slug]" value="{{old('post.post_slug') ? old('post.post_slug') : (!empty($post) ? $post->post_slug : '')}}">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Price<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <input type="tel" class="form-control m-input money-masking" placeholder="Product Price" name="product_meta[product_price]" value="{{old('product_meta.product_price') ? old('product_meta.product_price') : (!empty($post) && !empty($post->productMeta) ? $post->productMeta->product_price : '')}}">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Sale<span class="ml-1 m--font-warning" aria-required="true">(Optional)</span></label>
                                            </div>
                                            <div class="col">
                                                <input type="tel" class="form-control m-input money-masking" placeholder="Product Sale" name="product_meta[product_sale]" value="{{old('product_meta.product_sale') ? old('product_meta.product_sale') : (!empty($post) && !empty($post->productMeta) ? $post->productMeta->product_sale : '')}}">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Weight<span class="ml-1 m--font-warning" aria-required="true">(Optional)</span></label>
                                            </div>
                                            <div class="col">
                                                <input type="number" class="form-control m-input" placeholder="Product Weight" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_WEIGHT}}]" value="{{old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_WEIGHT) ? old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_WEIGHT) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_WEIGHT)) ? $post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_WEIGHT) : '')}}" step="0.01">
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Stock<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <input type="number" class="form-control m-input" placeholder="Product Stock" name="product_meta[product_stock]" value="{{old('product_meta.product_stock') ? old('product_meta.product_stock') : (!empty($post) && !empty($post->productMeta) ? $post->productMeta->product_stock : '')}}">
                                            </div>
                                        </div>
                                        {{-- <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Availablity<span class="ml-1 m--font-warning" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col d-flex align-items-center">
                                                <div class="m-form__group form-group">
                                                    <div class="m-radio-inline">
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[availability]" {{old('product_meta.availability') && (old('product_meta.availability') == 'in stock') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->availability == 'in stock') ? 'checked' : '')}} value="in stock"> In Stock
                                                            <span></span>
                                                        </label>
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[availability]" {{old('product_meta.availability') && (old('product_meta.availability') == 'out of stock') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->availability == 'out of stock') ? 'checked' : '')}} value="out of stock"> Out Of Stock
                                                            <span></span>
                                                        </label>
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[availability]" {{old('product_meta.availability') && (old('product_meta.availability') == 'preorder') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->availability == 'preorder') ? 'checked' : '')}} value="preorder"> Preorder
                                                            <span></span>
                                                        </label>
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[availability]" {{old('product_meta.availability') && (old('product_meta.availability') == 'available for order') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->availability == 'available for order') ? 'checked' : '')}} value="available for order"> Available For Order
                                                            <span></span>
                                                        </label>
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[availability]" {{old('product_meta.availability') && (old('product_meta.availability') == 'discontinued') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->availability == 'discontinued') ? 'checked' : '')}} value="discontinued"> Discontinued
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Product Condition<span class="ml-1 m--font-warning" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col d-flex align-items-center">
                                                <div class="m-form__group form-group">
                                                    <div class="m-radio-inline">
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[condition]" {{old('product_meta.condition') && (old('product_meta.condition') == 'new') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->condition == 'new') ? 'checked' : '')}} value="new"> New
                                                            <span></span>
                                                        </label>
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[condition]" {{old('product_meta.condition') && (old('product_meta.condition') == 'refurbished') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->condition == 'refurbished') ? 'checked' : '')}} value="refurbished"> Refurbished
                                                            <span></span>
                                                        </label>
                                                        <label class="m-radio">
                                                            <input type="radio" name="product_meta[condition]" {{old('product_meta.condition') && (old('product_meta.condition') == 'used') ? 'checked' : (!empty($post) && !empty($post->productMeta) && ($post->productMeta->condition == 'used') ? 'checked' : '')}} value="used"> Used
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0 flex-wrap">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Category<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                            </div>
                                            <div class="col">
                                                <select class="form-control m-input select2" name="taxonomy[category][]">
                                                    <option value="" selected disabled>-Select Category-</option>
                                                    @foreach ($categories as $category)
                                                        @if(old('taxonomy.category'))
                                                            <option value="{{$category->getKey()}}" {{in_array($category->getKey(), old('taxonomy.category')) ? 'selected' : ''}}>{{$category->term->name}}</option>
                                                        @else
                                                            <option value="{{$category->getKey()}}" {{!empty($post->taxonomies) && in_array($category->getKey(), $post->taxonomies->pluck(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getPrimaryKey())->toArray()) ? 'selected' : ''}}>{{$category->term->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0 flex-wrap">
                                            <div class="col-4 d-flex justify-content-end py-3">
                                                <label for="exampleInputEmail1">Tag</label>
                                            </div>
                                            <div class="col">
                                                <select class="form-control m-input taginput w-100" name="taxonomy[tag][]" multiple>
                                                    @if(old('taxonomy.tag'))
                                                        @foreach(old('taxonomy.tag') as $tag)
                                                            <option value="{{$tag}}">{{$tag}}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach ($tags as $tag)
                                                            @if(!empty($post->taxonomies) && in_array($tag->getKey(), $post->taxonomies->pluck(\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\TermTaxonomy::getPrimaryKey())->toArray()))
                                                                <option value="{{$tag->term->name}}">{{$tag->term->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group d-flex px-0">
                                            <div class="col-12">
                                                <textarea class="form-control m-input texteditor" placeholder="Product Content" name="post[post_content]">{{old('post.post_content') ? old('post.post_content') : (!empty($post) ? $post->post_content : '')}}</textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" name="post[post_excerpt]" value="{{old('post.post_excerpt') ? old('post.post_excerpt') : (!empty($post) ? $post->post_excerpt : '')}}">
                                    </div>
                                    @include('ecommerce::admin.v_1.partials.variant', ['id' => 'ecommerce'])
                                    @include('ecommerce::admin.v_1.partials.marketplace')
                                    @include('ecommerce::admin.v_1.partials.gallery')
                                </div>
                            </div>
                            {{csrf_field()}}
                            @if(isset($_GET['code']))
                                <input type="hidden" name="{{\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getPrimaryKey()}}" value="{{$_GET['code']}}">
                            @endif
                            {{$method}}

                        <!--end::Form-->
                    </div>
                </div>
                <div class="col-md-4">
                    @include('post::admin.v_1.partials.meta_data')
                </div>
            </div>
            <!--end::Portlet-->
        </form>

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_level_js')
    {{Html::script(module_asset_url('core:assets/js/autosize.min.js'))}}
    {{Html::script(module_asset_url('core:assets/js/slugify.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/ckeditor_4/ckeditor.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/global/plugins/typeahead/typeahead.bundle.min.js'))}}
    {{Html::script('vendor/laravel-filemanager/js/lfm.js')}}
@endsection

@section('page_script_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".money-masking").inputmask('999,999,999', {
                numericInput: true,
            });
        });
    </script>
    <script type="text/javascript">
        (function($){
            $(document).ready(function() {
                var tag = new Bloodhound({
                  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                  queryTokenizer: Bloodhound.tokenizers.whitespace,
                  prefetch: {
                    url: "{{action('\Gdevilbat\SpardaCMS\Modules\Taxonomy\Http\Controllers\TaxonomyController@getSuggestionTag')}}",
                    cache: false,
                    filter: function(list) {
                      return $.map(list, function(tag) {
                        return { name: tag };
                        });
                    }
                  }
                });
                tag.initialize();

                $('.taginput').tagsinput({
                  typeaheadjs: {
                    name: 'tag',
                    displayKey: 'name',
                    valueKey: 'name',
                    source: tag.ttAdapter()
                  }
                });
            });
        }(jQuery));
    </script>
@endsection