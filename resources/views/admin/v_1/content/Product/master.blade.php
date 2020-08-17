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
                            <div class="col">
                                <form action="{{route('cms.setting.store')}}" method="post">
                                    <div class="m-form__group form-group row justify-content-md-end mb-0">
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
                </div>

                <!--begin: Datatable -->
                <table class="table table-striped display responsive nowrap" id="data-product" width="100%" data-ajax="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@serviceMaster')}}" data-url-scrapping-product="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@scrappingProduct')}}" data-url-scrapping-variant="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@scrappingVariant')}}">
                    <thead>
                        <tr>
                            <th data-priority="1">ID</th>
                            <th data-priority="2">Title</th>
                            <th class="no-sort">Author</th>
                            <th class="no-sort" data-priority="9">Categories</th>
                            <th class="no-sort">Tags</th>
                            <th class="no-sort">Comment</th>
                            <th data-priority="10">Status</th>
                            <th data-priority="3">
                                Product <br>
                                Price
                            </th>
                            <th data-priority="4">
                                Product <br>
                                Sale
                            </th>
                            <th class="no-sort" data-priority="6">Supplier</th>
                            <th class="no-sort" data-priority="5">Store</th>
                            <th class="no-sort" data-priority="8">Availability</th>
                            <th>Created At</th>
                            <th class="no-sort" data-priority="7">Action</th>
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
    {{Html::script(module_asset_url('ecommerce:resources/views/admin/v_1/js/scrapper.js').'?id='.filemtime(module_asset_path('ecommerce:resources/views/admin/v_1/js/scrapper.js')))}}

    <script type="text/javascript">
        $(document).ready(function() {
            $("#data-product").DataTable( {
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": $.fn.dataTable.pipeline( {
                    url: $(this).attr('data-ajax'),
                    pages: 5 // number of pages to cache
                }),
                 "columnDefs": [
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
        });
    </script>
@endsection