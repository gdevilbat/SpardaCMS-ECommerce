@if((!empty($post->tokopedia_slug) && !empty($post->tokopedia_store)) || !empty($post->shopee_slug))
	@if(!empty($post->tokopedia_slug) && !empty($post->tokopedia_store))
		<a href="{{url('https://tokopedia.com/'.$post->tokopedia_store.'/'.$post->tokopedia_slug)}}" title="" target="_blank">
			<span data-index="{{$post->getKey()}}" class="tokopedia-store" id="tokopedia-store-{{$post->getKey()}}" data-url="{{url('https://tokopedia.com/'.$post->tokopedia_store.'/'.$post->tokopedia_slug)}}">
			</span>
			Tokopedia
		</a>
	@endif
	<hr>
	@if(!empty($post->shopee_slug))
		<a href="{{url('https://shopee.co.id/'.$post->shopee_slug)}}" title="" target="_blank">
			<span data-index="{{$post->getKey()}}" class="shopee-store" id="shopee-store-{{$post->getKey()}}" data-url="{{$post->shopee_slug}}">
			</span>
			Shopee
		</a>
	@endif
@else
	<span>-</span>
@endif