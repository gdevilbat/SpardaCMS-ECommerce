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
<div id="item-promotion" class="m-section mb-2" data-url="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@scheduleItem') }}" v-cloak>
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


@push('page_script_js')
    <script type="text/javascript">
        (function($){
            var ItemPromotion = new Vue({
                    el: "#item-promotion",
                    data: {
                        items: []
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
                            $('#data-product').DataTable( {
                                "processing": true,
                                "serverSide": true,
                                "order": [],
                                "ajax": $.fn.dataTable.pipeline( {
                                    url: '{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@serviceMaster')}}',
                                    pages: 5, // number of pages to cache
                                    data: {shop_id: $("[name='shop_id']").val()}
                                }),
                                 "columnDefs": [
                                ],
                                "drawCallback": function( settings ) {
                                    $(".item-promotion").click(function(event) {
                                        self.items.push({name: $(this).attr('data-name'), <?=\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::FOREIGN_KEY?>: parseInt($(this).attr('data-id')), status: $(this).attr('data-status')});
                                        self.items = self.getUnique(self.items, '<?=\Gdevilbat\SpardaCMS\Modules\Post\Entities\Post::FOREIGN_KEY?>');
                                    });
                                }
                            } );

                            $.ajax({
                                url: $("#item-promotion").attr('data-url'),
                            })
                            .done(function(response) {
                                self.items = response;
                            })
                            .fail(function() {
                                console.log("error");
                            });
                            
                        });
                    }
                });

            var BoostedItem = new Vue({
                el: "#boosted-item",
                data: {
                    items: [],
                },
                mounted: function(){
                    this.$nextTick(function(){
                        let self = this;
                        $.ajax({
                            url: $("#boosted-item").attr('data-url'),
                            type: 'POST',
                            data: {shop_id: $("[name='shop_id']").val()},
                            headers: {
                                "Accept": "application/json",
                                "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content'),
                            }
                        })
                        .done(function(response) {
                            let items = response.items;
                            $.each(items, function(index, el) {
                                $.ajax({
                                    url: $("#boosted-item").attr('data-url-item'),
                                    type: 'POST',
                                    data: {shop_id: $("[name='shop_id']").val(), product_id: el.item_id,},
                                    headers: {
                                        "Accept": "application/json",
                                        "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                                    }
                                }).done(function(response){
                                    self.items.push(response);
                                });
                            });
                        });
                    });
                }
            });   
        })(jQuery);
    </script>
@endpush