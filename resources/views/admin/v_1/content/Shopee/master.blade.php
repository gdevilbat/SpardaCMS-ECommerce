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
                <!--begin: Datatable -->
                <table class="table table-striped display responsive nowrap" id="data-product" width="100%">
                    <thead>
                        <tr>
                            <th data-priority="2">ID</th>
                            <th data-priority="3">Title</th>
                            <th class="no-sort" data-priority="4">Price</th>
                            <th class="no-sort" data-priority="5">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!--end: Datatable -->
                <hr>
                <div id="item-promotion" class="m-section mb-2">
                    <h3 class="m-section__heading">Item Scheduled</h3>
                    <form action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@saveItemScheduled') }}" method="post">
                        <div v-for="(item, index) in items">
                            @{{ item.name }} - @{{ item.status }} <i class="la la-close" v-on:click="removeComponent(index)" style="cursor: pointer;"></i>
                            <input type="hidden" v-bind:name="'items[]'" v-model="items[index]['<?=\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::FOREIGN_KEY?>']">
                        </div>
                        {{ csrf_field() }}
                        <div class="d-flex mt-1 justify-content-end">
                            <button type="submit" class="btn m-btn--square  m-btn m-btn--gradient-from-brand m-btn--gradient-to-info">Save</button>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="row" id="boosted-item" data-url="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\ShopeeController@itemGetBoosted') }}" data-url-item="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\ShopeeController@itemGetDetail') }}" v-cloak>
                    <div class="col-md-3 my-1 position-relative" v-for="item in items">
                        <img class="img-fluid" v-bind:src="item.images[0]" alt="">
                        <div class="position-absolute text-light bg-info px-1" style="bottom: 0px;left: 0px;margin: 0px 15px">
                            @verbatim
                                <span>{{ item.name }}</span>
                            @endverbatim
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

    <script type="text/javascript">
        var ItemPromotion = new Vue({
            el: "#item-promotion",
            data: {
                items: {!! !empty($items) ? json_encode($items) : json_encode(array()) !!}
            },
            methods:{
                getUnique: function(arr, comp){
                     // store the comparison  values in array
                       const unique =  arr.map(e => e[comp])

                                      // store the indexes of the unique objects
                                      .map((e, i, final) => final.indexOf(e) === i && i)

                                      // eliminate the false indexes & return unique objects
                                     .filter((e) => arr[e]).map(e => arr[e]);

                       return unique;
                },
                removeComponent: function(index){
                    this.items.splice(index, 1);
                },
            },
            mounted: function(){

                this.$nextTick(function(){
                    let self = this;
                    $(document).ready(function() {
                        $('#data-product').DataTable( {
                            "processing": true,
                            "serverSide": true,
                            "order": [],
                            "ajax": $.fn.dataTable.pipeline( {
                                url: '{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@serviceMaster')}}',
                                pages: 5 // number of pages to cache
                            }),
                             "columnDefs": [
                            ],
                            "drawCallback": function( settings ) {
                                $(".item-promotion").click(function(event) {
                                    let url = $(this).attr('data-shopee-url');
                                    let shopee = url.split('/').slice(-2);
                                    self.items.push({name: $(this).attr('data-name'), <?=\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::FOREIGN_KEY?>: parseInt($(this).attr('data-id')), status: $(this).attr('data-status')});
                                    self.items = self.getUnique(self.items, '<?=\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::FOREIGN_KEY?>');
                                });
                            }
                        } );
                    } );
                });
            }
        });        
    </script>
@endsection