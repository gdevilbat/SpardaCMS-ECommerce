@if((!empty($tokopedia_store) && $tokopedia_store->slug != '') || (!empty($shopee_store) && $shopee_store->shop_id != ''))
	@if(!empty($tokopedia_store) && $tokopedia_store->slug != '')
		<a href="{{url('https://tokopedia.com/'.$tokopedia_store->merchant.'/'.$tokopedia_store->slug)}}" title="" target="_blank">
			<span data-index="{{$post->getKey()}}" class="tokopedia-store" id="tokopedia-store-{{$post->getKey()}}" data-url="{{url('https://tokopedia.com/'.$tokopedia_store->merchant.'/'.$tokopedia_store->slug)}}">
			</span>
			Tokopedia
		</a>
	@endif
	<hr>
	@if(!empty($shopee_store) && $shopee_store->shop_id != '')
		<a href="{{url('https://shopee.co.id/product/'.$shopee_store->shop_id.'/'.$shopee_store->product_id)}}" title="" target="_blank">
			<span data-index="{{$post->getKey()}}" class="shopee-store" id="shopee-store-{{$post->getKey()}}" data-url="{{'product/'.$shopee_store->shop_id.'/'.$shopee_store->product_id}}">
			</span>
			Shopee
		</a>
	@endif
	<span data-index="{{$post->getKey()}}" id="shopee-weight-{{$post->getKey()}}">
	</span>
@else
	<span>-</span>
@endif