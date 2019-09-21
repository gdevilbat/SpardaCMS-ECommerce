@if(!empty($post->tokopedia_slug) && !empty($post->tokopedia_store))
	<a href="{{url('https://tokopedia.com/'.$post->tokopedia_store.'/'.$post->tokopedia_slug)}}" title="" target="_blank">
		<span data-index="{{$post->getKey()}}" class="scrapping-store" id="scrapping-store-{{$post->getKey()}}" data-url="{{url('https://tokopedia.com/'.$post->tokopedia_store.'/'.$post->tokopedia_slug)}}">
			Tokopedia
		</span>
	</a>
@else
	<span>-</span>
@endif