<table class="table table-striped table-bordered display responsive nowrap" id="discount-list" v-cloak width="100%">
    <thead>
        <tr>
            <th data-priority="3">Title</th>
            <th class="no-sort" data-priority="5">Status</th>
            <th class="no-sort" data-priority="6">Action</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="(item, index) in items">
            <td>@{{ item.discount_name }}</td>
            <td>@{{ item.status }}</td>
            <td><a href="javascript:void(0)" v-on:click="DiscountItem.showItem(item.discount_id)" v-if="item.status == 'ongoing' || item.status == 'upcoming'">Show</a></td>
        </tr>
    </tbody>
</table>

<hr> 

<div class="row" id="discount-item" v-cloak>
    <button type="button" class="btn m-btn m-btn--gradient-from-success m-btn--gradient-to-accent ml-auto mb-2" v-on:click="addDiscountItem()" v-if="available_items.length > 0 && DiscountItem.discount_id != '' && DiscountItem.items.length < 80">Upload Discount</button>
    <table class="w-100 table table-striped table-bordered display responsive nowrap">
        <thead>
            <tr>
                <th><input id="data-checklist" type="checkbox"></th>
                <th>Product Name</th>
                <th>Status</th>
                <th>Price</th>
                <th>Sale</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="available_item in available_items">
                <td><input class="data-checklist" type="checkbox" v-bind:data-index="available_item.id_posts"></td>
                <td>@{{ available_item.post_title }}</td>
                <td>@{{ available_item.product_meta.availability }}</td>
                <td>@{{ available_item.product_meta.product_price }}</td>
                <td>@{{ available_item.product_meta.product_sale }}</td>
            </tr>
        </tbody>
    </table>

    <hr>

    <div class="col-md-3 my-1 position-relative" v-for="item in items">
        <a v-bind:href="'https://shopee.co.id/product/'+ $('[name=\'shop_id\']').val()+'/'+item.item_id" target="_blank">
            {{-- <img class="img-fluid" v-bind:src="item.images[0]" alt=""> --}}
            <div class="text-light bg-info px-1" style="bottom: 0px;left: 0px;margin: 0px 15px">
                @verbatim
                    <span>{{ item.item_name }}</span>
                @endverbatim
            </div>
        </a>
    </div>
</div>

@push('page_script_js')
    <script type="text/javascript">
        var DiscountList = new Vue({
            el: "#discount-list",
            data: {
                items: [],
                page: 1
            },
            methods:{
                showDiscounts: function(){
                    let self = this;
                    $.ajax({
                        url: "{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getDiscountsList') }}",
                        data: {shop_id: $("[name='shop_id']").val(), page: self.page}
                    })
                    .done(function(response) {
                        self.items = response.discount;
                        DiscountItem.available_items = response.available_items;
                        self.page++;
                    })
                    .fail(function() {
                        console.log("error");
                    });
                }
            },
            mounted: function(){
                this.$nextTick(function(){
                    this.showDiscounts();
                });
            }
        });

        var DiscountItem = new Vue({
            el: "#discount-item",
            data: {
                items: [],
                available_items: [],
                page: '',
                discount_id: ''
            },
            methods: {
                addDiscountItem: function(){
                    let self = this;
                    let post_id = [];

                    $(".data-checklist:checked").each(function(index, el) {
                        post_id.push($(this).attr('data-index'));
                    });

                    $.ajax({
                        url: "{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@addDiscountItem') }}",
                        type: 'POST',
                        data: {shop_id: $("[name='shop_id']").val(), post_id: post_id, discount_id: self.discount_id},
                        headers: {
                            "Accept": "application/json",
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function(response) {
                        self.showItem(self.discount_id);
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                    
                },
                showItem: function(id, page = 1){
                    let self = this;
                    this.discount_id = id;

                    $.ajax({
                        url: '{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getDiscountDetail') }}',
                        data: {shop_id: $("[name='shop_id']").val(), discount_id: self.discount_id, page: page},
                    })
                    .done(function(response) {
                       self.items = response.discount.items;
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                }
            }
        });
    </script>
@endpush