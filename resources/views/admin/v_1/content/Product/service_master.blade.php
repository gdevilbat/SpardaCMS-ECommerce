<div class="col">
    <div class="btn-group">
        <a href="javascript:void(0)" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions
        </a>
        <div class="dropdown-menu dropdown-menu-left">
            <button class="dropdown-item" type="button">
                <a class="m-link m-link--state m-link--warning" href="{{$post->post_url}}" target="_blank"><i class="fa fa-eye"> Preview</i></a>
            </button>
            @can('update-ecommerce', $post)
                <button class="dropdown-item" type="button">
                    <a class="m-link m-link--state m-link--info" href="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@create').'?code='.encrypt($post->getKey())}}"><i class="fa fa-edit"> Edit</i></a>
                </button>
            @endcan
            @can('delete-ecommerce', $post)
                <form action="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@destroy')}}" method="post" accept-charset="utf-8">
                    {{method_field('DELETE')}}
                    {{csrf_field()}}
                    <input type="hidden" name="{{\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::getPrimaryKey()}}" value="{{encrypt($post->getKey())}}">
                </form>
                <button class="dropdown-item confirm-delete" type="button"><a class="m-link m-link--state m-link--accent" data-toggle="modal" href="#small"><i class="fa fa-trash"> Delete</i></a></button>
            @endcan
            <hr>
            @can('update-ecommerce', $post)
                @php
                    $shopee_store = $post->meta->getMetaData(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE);
                @endphp
                @if((!empty($shopee_store) && $shopee_store->shop_id == '') || empty($shopee_store))
                    <button class="dropdown-item" type="button">
                        <a class="m-link m-link--state m-link--info" href="javascript:void(0)" onclick="ShopeeUpload.setDataForm('{{encrypt($post->getKey())}}')"><i class="fa fa-upload"> Shopee Upload</i></a>
                    </button>
                @endif
            @endcan
            @can('update-ecommerce', $post)
                @php
                    $lazada_store = $post->meta->getMetaData(\Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::LAZADA_STORE);
                @endphp
                @if((!empty($lazada_store) && $lazada_store->shop_id == '') || empty($lazada_store))
                    <button class="dropdown-item" type="button">
                        <a class="m-link m-link--state m-link--info" href="javascript:void(0)" onclick="LazadaUpload.setDataForm('{{encrypt($post->getKey())}}')"><i class="fa fa-upload"> Lazada Upload</i></a>
                    </button>
                @endif
            @endcan
        </div>
    </div>
</div>