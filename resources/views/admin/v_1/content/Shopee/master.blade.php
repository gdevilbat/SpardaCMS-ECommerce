@extends('core::admin.'.$theme_cms->value.'.templates.parent')

@section('title_dashboard', ' Product')

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

        <!--begin::Portlet-->
        <div class="m-portlet m-portlet--tab">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <span class="m-portlet__head-icon m--hide">
                            <i class="fa fa-gear"></i>
                        </span>
                        <h3 class="m-portlet__head-text">
                            Master Data of Shoppe Promotion
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
                <!--begin: Datatable -->
                <table class="table table-striped display responsive nowrap" id="data-product" width="100%" data-ajax="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@serviceMaster')}}" data-url-scrapping-product="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ScrappingController@scrappingProduct')}}" data-url-scrapping-variant="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ScrappingController@scrappingVariant')}}" data-url-scrapping-shopee="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ScrappingController@scrappingShopee')}}" data-url-shopee-detail="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getItemDetail')}}">
                    <thead>
                        <tr>
                        	<th class="no-sort" data-priority="1"></th>
                            <th data-priority="2">
                            	<input id="data-checklist" type="checkbox">
							</th>
                            <th data-priority="3">ID</th>
                            <th data-priority="4">Title</th>
                            <th class="no-sort">Author</th>
                            <th class="no-sort" data-priority="12">Categories</th>
                            <th class="no-sort">Tags</th>
                            <th class="no-sort">Comment</th>
                            <th data-priority="11">Status</th>
                            <th data-priority="5">
                                Product <br>
                                Price
                            </th>
                            <th data-priority="6">
                                Product <br>
                                Sale
                            </th>
                            <th class="no-sort" data-priority="8">Supplier</th>
                            <th class="no-sort" data-priority="7">Store</th>
                            <th class="no-sort" data-priority="10">Availability</th>
                            <th>Created At</th>
                            <th class="no-sort" data-priority="9">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <!--end: Datatable -->
            </div>


        </div>

        <!--end::Portlet-->

    </div>
</div>
{{-- End of Row --}}

@endsection

@section('page_script_js')
    {{Html::script(module_asset_url('ecommerce:resources/views/admin/v_1/js/shopee.js').'?id='.filemtime(module_asset_path('ecommerce:resources/views/admin/v_1/js/shopee.js')))}}
@endsection