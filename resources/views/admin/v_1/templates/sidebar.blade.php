@can('menu-ecommerce')
    <li class="m-menu__item m-menu__item--submenu {{in_array(Route::current()->getName(), ['product', 'product-category', 'product-tag', 'marketplace-shopee']) ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
        <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
            <i class="m-menu__link-icon flaticon-price-tag"></i>
                <span class="m-menu__link-text">Product</span>
            <i class="m-menu__ver-arrow la la-angle-right"></i>
         </a>
        <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
            <ul class="m-menu__subnav">
                <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true"><span class="m-menu__link"><span class="m-menu__link-text">Product</span></span></li>
                <li class="m-menu__item  {{Route::current()->getName() ==  'product' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">All Product</span></a></li>
                <li class="m-menu__item  {{Route::current()->getName() ==  'product-category' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\CategoryController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Categories</span></a></li>
                <li class="m-menu__item  {{Route::current()->getName() ==  'product-tag' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TagController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Tags</span></a></li>
                <li class="m-menu__item  {{Route::current()->getName() ==  'marketplace-shopee' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@shopeePromotion')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Shopee Promotion</span></a></li>
            </ul>
        </div>
    </li>
@endcan