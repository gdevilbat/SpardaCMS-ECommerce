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
            <td><a href="javascript:void(0)" v-on:click="DiscountItem.showItem(item.discount_id)">Show</a></td>
        </tr>
    </tbody>
</table>

<hr> 

<div class="row" id="discount-item">
    <table class="w-100 table table-striped table-bordered display responsive nowrap">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Status</th>
                <th>Price</th>
                <th>Sale</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="available_item in available_items">
                <td>@{{ available_item.post_title }}</td>
                <td>@{{ available_item.product_meta.availability }}</td>
                <td>@{{ available_item.product_meta.product_price }}</td>
                <td>@{{ available_item.product_meta.product_sale }}</td>
                <td><button type="button" class="btn m-btn m-btn--gradient-from-success m-btn--gradient-to-accent" v-on:click="addDiscountItem(available_item.id_posts)">Upload Discount</button></td>
            </tr>
        </tbody>
    </table>

    <hr>

    <div class="col-md-3 my-1 position-relative" v-for="item in items">
        <img class="img-fluid" v-bind:src="item.images[0]" alt="">
        <div class="position-absolute text-light bg-info px-1" style="bottom: 0px;left: 0px;margin: 0px 15px">
            @verbatim
                <span>{{ item.name }}</span>
            @endverbatim
        </div>
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
            mounted: function(){
                this.$nextTick(function(){
                    let self = this;
                    $.ajax({
                        url: "{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getDiscountsList') }}",
                        data: {shop_id: $("[name='shop_id']").val(), page: self.page}
                    })
                    .done(function(response) {
                        self.items = response.discount;
                        self.page++;
                    })
                    .fail(function() {
                        console.log("error");
                    });
                    
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
                addDiscountItem: function(post_id){
                    let self = this;
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
                       let items = response.discount.items;
                       self.available_items = response.available_items;
                       self.items = [];
                        $.each(items, function(index, el) {
                            $.ajax({
                                url: "{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\ShopeeController@itemGetDetail') }}",
                                type: 'POST',
                                data: {shop_id: $("[name='shop_id']").val(), product_id: el.item_id},
                                headers: {
                                    "Accept": "application/json",
                                    "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                                }
                            }).done(function(response){
                                self.items.push(response);
                            });
                        });
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