$(document).ready(function() {

    $("#shopee-sycronize").click(function(event) {
        if(window.confirm("Apakah Anda Yakin Ingin Update Data Shopee Sesuai Ecommerce ?"))
        {
            let counter_shopee_sycronize = 0;
            $(".data-checklist:checked").each(function(index, el) {
                let url = $('#shopee-store-'+$(this).attr('data-index')).attr('data-url');
                let shopee = url.split('/').slice(-2);
                let post_id = $(this).attr('data-index');
                $.ajax({
                    url: $("#shopee-sycronize").attr('data-url-update'),
                    type: 'POST',
                    data: {'shop_id': shopee[0], 'product_id': shopee[1], 'post_id': post_id},
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer "+ $("[name='scrapping[token]']").val()
                    }
                }).done(function(response){
                    counter_shopee_sycronize++;
                    if(counter_shopee_sycronize >= $(".data-checklist:checked").length)
                        table.ajax.reload( null, false );
                });
            });
        }
    });

    if($("#boosted-item").length > 0){
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
                            "Authorization": "Bearer "+ $("[name='scrapping[token]']").val(),
                        }
                    })
                    .done(function(response) {
                        let items = response;
                        $.each(items, function(index, el) {
                            $.ajax({
                                url: $("#boosted-item").attr('data-url-item'),
                                type: 'POST',
                                data: {shop_id: $("[name='shop_id']").val(), product_id: el.item_id,},
                                headers: {
                                    "Accept": "application/json",
                                    "Authorization": "Bearer "+ $("[name='scrapping[token]']").val()
                                }
                            }).done(function(response){
                                self.items.push(response);
                            });
                        });
                    });
                });
            }
        });
    }
    
    if($("#shopee-info").length > 0){
        var ShopeeInfo = new Vue({
            el: "#shopee-info",
            data: {
                store: []
            },
            mounted: function(){
                this.$nextTick(function(){
                    let self = this;
                    $.ajax({
                        url: $("#shopee-info").attr('data-url'),
                        type: 'POST',
                        data: {shop_id: $("[name='shop_id']").val()},
                        headers: {
                            "Accept": "application/json",
                            "Authorization": "Bearer "+ $("[name='scrapping[token]']").val()
                        }
                    })
                    .done(function(response) {
                       self.store = response;
                    });
                });
            }
        });
    }
});

var ShopeeUpload = new Vue({
    el: "#shopee_upload",
    data: {
        item: null,
        category_id: null,
        categories: [],
        selected_category: [],
        children_categories: [],
    },
    methods: {
        addSelected: function(e, index_selected){
            self = this;
            var tmp = this.categories;
            this.children_categories = [];
            this.category_id = null;
            this.$set(this.selected_category, index_selected, e.target.value);

            this.$nextTick(function () {
                $.each(this.selected_category, function(index, val) {

                     if(index_selected <= index)
                     {
                        self.selected_category.splice(index+1, 1);
                        document.getElementsByName('children_'+(index+1))[0].value = "";
                     }
                });
            });


            $.each(self.selected_category, function(index, el) {
                if(tmp[el].has_children)
                {
                    self.$set(self.children_categories, index, tmp[el].children);
                    tmp = tmp[el].children;
                }
                else{
                    self.selected_category.splice(index, 1);
                    self.category_id = tmp[el].category_id;
                }
            });
        }
    }
});
window.ShopeeUpload = ShopeeUpload;

window.popupWindow = function(url, windowName, win, w, h) {
    const y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2);
    const x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2);
    return win.open(url, windowName, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=${w}, height=${h}, top=${y}, left=${x}`);
}