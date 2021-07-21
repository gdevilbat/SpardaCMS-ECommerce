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
                            Shopee Marketplace
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
                <div class="d-flex">
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
                <input type="hidden" class="form-control m-input" name="shop_id" value="{{getSettingConfig('shopee_id')}}">
                <div class="">
                    <a onclick="popupWindow('{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@authentication').'?'.http_build_query(['callback' => 'refreshAndClose']) }}', 'test', window, 1024 , 600);" href="javascript:void(0)" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                        <span>
                            <i class="la la-refresh"></i>
                            <span>Authentication Shopee</span>
                        </span>
                    </a>
                </div>
                <div id="shopee-info" data-url="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\ShopeeController@shopGetDetail') }}" v-cloak>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label text-right">Shop Name :</label>
                        <div class="col">
                           <label for="example-text-input" class="col-form-label">@{{ store.shop_name }}</label>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label text-right">Shop Description :</label>
                        <div class="col">
                           <label for="example-text-input" class="col-form-label">@{{ store.shop_description }}</label>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label text-right">Product Limit :</label>
                        <div class="col">
                           <label for="example-text-input" class="col-form-label">@{{ store.item_limit }}</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="m-portlet m-portlet--tabs m-portlet--success m-portlet--head-solid-bg m-portlet--bordered">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary" role="tablist">
                                <li class="nav-item m-tabs__item">
                                    <a class="nav-link m-tabs__link active show" data-toggle="tab" href="#promotion" role="tab" aria-selected="false">
                                        <i class="la la-cog"></i> Promotion
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="promotion" role="tabpanel">
                                @include('ecommerce::admin.v_1.partials.shopee.promotion')
                            </div>
                        </div>
                    </div>
                </div>
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