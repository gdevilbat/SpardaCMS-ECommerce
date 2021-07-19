$(document).ready(function() {
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
                            "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
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

(function($){
    var ShopeeUpload = new Vue({
        el: "#shopee_upload",
        data: function (){
            return initialState();
        }, 
        methods: {
            addSelected: function(e, index_selected){
                let self = this;
                let tmp = this.categories;
                this.children_categories = [];
                this.attributes = [];
                this.category_id = null;
                this.$set(this.selected_category, index_selected, e.target.value);

                this.$nextTick(function () {
                    $.each(this.selected_category, function(index, val) {

                         if(index_selected <= index)
                         {
                            self.selected_category.splice(index+1, 1);
                            document.getElementById('children_'+(index+1)).value = "";
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
                        $.ajax({
                            url: $("#shopee_form").attr('data-url-shopee-attribute'),
                            data: {category_id: self.category_id, shop_id: $("[name='shop_id']").val()},
                        })
                        .done(function(response) {
                            self.attributes = response.attributes;
                        })
                        .fail(function() {
                            console.log("error");
                        });
                        
                    }
                });
            },
            setAttributeIndex: function(index){
                this.attribute_option_index = index;
            },
            addAttributeOption: function(index){
                last = this.attributes[index].options.length;
                this.$set(this.attributes[index].options, last, $('#attribute_option_'+index).val());
                this.attribute_option_index = null;
            },
            setDataForm: function(id){
                this.id_posts = id;
                let self = this;
                $.ajax({
                    url: $("#shopee_form").attr('data-url-product-detail'),
                    type: 'POST',
                    data: {id_posts: this.id_posts},
                    headers: {
                        "Accept": "application/json",
                    }
                })
                .done(function(response) {
                    self.item = response;
                    $('#modal-shopee-upload').modal('show');

                    $.ajax({
                        url: $("#shopee_form").attr('data-url-shopee-logistics'),
                         data: {shop_id: $("[name='shop_id']").val()},
                    })
                    .done(function(response) {
                        self.logistics = response;
                    })
                    .fail(function() {
                        console.log("error");
                    });

                    if(window.objSize(response.product_variant) > 0)
                    {
                        Variant_shopee_upload.variants = response.product_variant.variants;
                        Variant_shopee_upload.children = response.product_variant.children;
                    }
                    else
                    {
                        Variant_shopee_upload.variants = [];
                        Variant_shopee_upload.children = [];
                    }

                })
                .fail(function() {
                    console.log("error");
                });

            },
            resetWindow: function(){
                 Object.assign(this.$data, initialState());
            },
            submit: function(event){
                event.preventDefault();
                let self = this;
                $.ajax({
                    url: $("#shopee_form").attr('data-action'),
                    type: 'POST',
                    data: $("#shopee_form").serialize(),
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                    }
                })
                .done(function() {
                    self.errors = [];
                    $("#reload-datatable").click();
                    $("#modal-shopee-upload").modal('hide');
                })
                .fail(function(response) {
                    self.errors = response.responseJSON.errors;
                });
                
            }
        }
    });

    window.ShopeeUpload = ShopeeUpload;
    
    function initialState (){
      return {
        id_posts: null,
        item: [],
        category_id: null,
        categories: [],
        logistics: [],
        attributes: [],
        selected_category: [],
        children_categories: [],
        selected_logistic: [],
        errors: [],
        attribute_option_index: null,
        is_pre_order: false
      }
    }


    $(document).ready(function() {
        $("#modal-shopee-upload").on("hidden.bs.modal", function(e){
            ShopeeUpload.resetWindow();
            Variant_shopee_upload.resetWindow();
        });

        $("#modal-shopee-upload").on('shown.bs.modal', function(){
            $.ajax({
                url: $("#shopee_form").attr('data-url-shopee-category'),
                data: {shop_id: $("[name='shop_id']").val()},
              })
              .done(function(response) {
                ShopeeUpload.categories = response;
              })
              .fail(function() {
                console.log("error");
              });
       });
    });

}(jQuery));


window.popupWindow = function(url, windowName, win, w, h) {
    const y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2);
    const x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2);
    return win.open(url, windowName, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=${w}, height=${h}, top=${y}, left=${x}`);
}