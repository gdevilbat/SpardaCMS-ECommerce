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
                            Master Data of Product
                        </h3>
                    </div>
                </div>
            </div>

            <div class="m-portlet__body">
                <div class="col-md-5">
                    @if (!empty(session('global_message')))
                        <div class="alert {{session('global_message')['status'] == 200 ? 'alert-info' : 'alert-warning' }} alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
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

                <div class="row mb-4">
                    @if(Auth::user()->can('create-ecommerce'))
                            <div class="col-md-5">
                                <a href="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@create')}}" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>Add New Product</span>
                                    </span>
                                </a>
                            </div>
                    @else
                            <div class="col-md-5">
                                <a href="javascript:void(0)" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air" data-toggle="m-popover" title="" data-content="You're not Allowed To Take This Action. Pleas Ask Admin !!!" data-original-title="Forbidden Action">
                                    <span>
                                        <i class="la la-ban"></i>
                                        <span>Add New Product</span>
                                    </span>
                                </a>
                            </div>
                    @endif
                            <div class="col row">
                                <div class="col">
                                    <form action="{{route('cms.setting.store')}}" method="post">
                                        <div class="m-form__group form-group row justify-content-end mb-0">
                                            <label class="col-8 col-form-label text-right">Syncronize Ecommerce :</label>
                                            <div>
                                                <span class="m-switch m-switch--icon m-switch--success">
                                                    <label>
                                                        <input type="checkbox" id="syncronize_ecommerce" {{!empty($settings->where('name', 'syncronize_ecommerce')->flatten()->first()->value) && $settings->where('name', 'syncronize_ecommerce')->flatten()->first()->value == 'true' ? 'checked' : ''}}>
                                                        <span></span>
                                                        <input type="hidden" name="syncronize_ecommerce" value="{{!empty($settings->where('name', 'syncronize_ecommerce')->flatten()->first()->value) && $settings->where('name', 'syncronize_ecommerce')->flatten()->first()->value == 'true' ? 'true' : 'false'}}">
                                                    </label>
                                                </span>
                                            </div>
                                        </div>
                                        {{csrf_field()}}
                                        {{method_field('PUT')}}
                                    </form>
                                </div>
                                <div class="text-right">
                                    <a href="#" class="btn btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill m-btn--air" data-toggle="modal" data-target="#setting-scrapping">
                                        <i class="la la-gear"></i>
                                    </a>
                                </div>
                                <div class="col-12 justify-content-end d-flex">
                                    <a href="javascript:void(0)" id="reload-datatable" class="btn btn-info m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                        <span>
                                            <i class="la la-refresh"></i>
                                            <span>Reload</span>
                                        </span>
                                    </a>
                                    <div class="btn-group">
                                        <button class="btn m-btn--pill m-btn m-btn--gradient-from-danger m-btn--gradient-to-accent dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Markeplace
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 5px !important; left: 0px; transform: translate3d(-27px, 40px, 0px);">
                                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#web-scrapping"><i class="la la-download"></i> Tokopedia Scrappping</a>
                                            <a class="dropdown-item" href="javascript:void(0)" id="shopee-sycronize" data-url-update="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@itemUpdate') }}"><i class="la la-compress"></i> Shopee Syncronize</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>

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

<div class="modal fade" id="setting-scrapping" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel">
  <div class="modal-dialog">
      <div class="modal-content">
          <form action="{{route('cms.setting.store')}}" method="post" accept-charset="utf-8">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Scrapping Setting</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                  {{-- <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">URL</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" name="scrapping[url]" placeholder="https://[API Scrapping]" value="{{getSettingConfig('scrapping', 'url')}}">
                        </div>
                    </div> --}}
					<div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Suplier Sync Mode</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <div class="m-radio-inline">
                                <label class="m-radio">
                                    <input type="radio" name="scrapping[suplier_sync]" value="ajax" {{getSettingConfig('scrapping', 'suplier_sync') == 'ajax' ? 'checked' : ''}}> Ajax
                                    <span></span>
                                </label>
                                <label class="m-radio">
                                    <input type="radio" name="scrapping[suplier_sync]" value="cloud" {{getSettingConfig('scrapping', 'suplier_sync') == 'cloud' ? 'checked' : ''}}> Cloud
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="m-form__group form-group row  d-md-flex">
                        <div class="ml-4 col-col-md-8">
                            <a onclick="popupWindow('{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@authentication') }}', 'test', window, 800, 600);" href="javascript:void(0)" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                <span>
                                    <i class="la la-refresh"></i>
                                    <span>Authentication Shopee</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Store Sync Mode</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <div class="m-radio-inline">
                                <label class="m-radio">
                                    <input type="radio" name="scrapping[store_sync]" value="ajax" {{getSettingConfig('scrapping', 'store_sync') == 'ajax' ? 'checked' : ''}}> Ajax
                                    <span></span>
                                </label>
                                <label class="m-radio">
                                    <input type="radio" name="scrapping[store_sync]" value="cloud" {{getSettingConfig('scrapping', 'store_sync') == 'cloud' ? 'checked' : ''}}> Cloud
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__group form-group row  d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Weight Check</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <span class="m-switch m-switch--icon m-switch--success">
                                <label>
                                    <input type="checkbox" id="weight_check" {{getSettingConfig('scrapping', 'weight_check') == 'true' ? 'checked' : ''}}>
                                    <span></span>
                                    <input type="hidden" name="scrapping[weight_check]" value="{{getSettingConfig('scrapping', 'weight_check') == 'true' ? 'true' : 'false'}}">
                                </label>
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Token</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" name="scrapping[token]" placeholder="ex: 9234kjkiwerwer8834" value="{{getSettingConfig('scrapping', 'token')}}">
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
              </div>
              {{csrf_field()}}
              {{method_field('PUT')}}
          </form>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="web-scrapping" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <form action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TokopediaController@getData') }}" method="post" accept-charset="utf-8" id="data-web-scrapping">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Web Scrapping</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Store</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" name="store_name" placeholder="Store Name" value="">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Page</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" min="1" class="form-control m-input" name="page" placeholder="Pagination Number" value="">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Limit</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" min="1" class="form-control m-input" name="limit" placeholder="Default: 80" value="">
                        </div>
                    </div>
                    <div id="data_scrapping" class="row" v-cloak>
                        <div class="col-12">
                            <span v-for="item in items">
                                <a v-bind:href="item.url" target="_blank"><span class="badge badge-info">@{{ item.product_name }}</span></a>&nbsp;
                            </span>
                            <span v-if="window.objSize(items) > 0">| @{{ window.objSize(items) }} Item</span>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                  <button type="button" id="submit-data-scrapping" class="btn btn-primary">Scan</button>
                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
              </div>
          </form>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

@endsection

@section('page_script_js')
    {{Html::script(module_asset_url('ecommerce:resources/views/admin/v_1/js/scrapper.js').'?id='.filemtime(module_asset_path('ecommerce:resources/views/admin/v_1/js/scrapper.js')))}}

    <script type="text/javascript">
        var table;
        $(document).ready(function() {
            table = $("#data-product").DataTable( {
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": $.fn.dataTable.pipeline( {
                    url: $(this).attr('data-ajax'),
                    pages: 5 // number of pages to cache
                }),
                 "columnDefs": [
                 { orderable: false, targets: [1] }
                ],
                "drawCallback": function( settings ) {
                    deleteData();
                    if($("[name='syncronize_ecommerce']").val() == 'true')
                    {
                        tokopediaScrap();
                    }
                },
                "initComplete": function(settings, json) {
                    var $searchBox = $("div.dataTables_filter input");
                    $searchBox.unbind();
                    var searchDebouncedFn = debounce(function() {
                        var api = new $.fn.dataTable.Api( settings );
                        api.search( this.value ).draw();
                    }, 1000);
                    $searchBox.on("keyup", searchDebouncedFn);
                }
            } );

            $("#reload-datatable").click(function(event) {
                table.ajax.reload( null, false );
            });
        });
    </script>

    {{Html::script(module_asset_url('ecommerce:resources/views/admin/v_1/js/shopee.js').'?id='.filemtime(module_asset_path('ecommerce:resources/views/admin/v_1/js/shopee.js')))}}
    {{Html::script(module_asset_url('ecommerce:resources/views/admin/v_1/js/tokopedia.js').'?id='.filemtime(module_asset_path('ecommerce:resources/views/admin/v_1/js/tokopedia.js')))}}
@endsection