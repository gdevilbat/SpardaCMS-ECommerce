(function($){
    var LazadaUpload = new Vue({
        el: "#lazada_upload",
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
                    if(tmp[el].hasOwnProperty('children'))
                    {
                        self.$set(self.children_categories, index, tmp[el].children);
                        tmp = tmp[el].children;
                    }
                    else{
                        self.selected_category.splice(index, 1);
                        self.category_id = tmp[el].category_id;
                        $.ajax({
                            url: $("#lazada_form").attr('data-url-lazada-attribute'),
                            data: {category_id: self.category_id, shop_id: $("[name='shop_id']").val()},
                            headers: {
                                "Accept": "application/json",
                                "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                            }
                        })
                        .done(function(response) {
                            self.attributes = response;
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
                this.$set(this.attributes[index].options, last, {'name': $('#attribute_option_'+index).val()});
                this.attribute_option_index = null;
            },
            setDataForm: function(id){
                this.id_posts = id;
                let self = this;
                $.ajax({
                    url: $("#lazada_form").attr('data-url-product-detail'),
                    type: 'POST',
                    data: {id_posts: this.id_posts},
                    headers: {
                        "Accept": "application/json",
                    }
                })
                .done(function(response) {
                    self.item = response;
                    $('#modal-lazada-upload').modal('show');

                    if(window.objSize(response.product_variant) > 0)
                    {
                        Variant_lazada_upload.variants = response.product_variant.variants;
                        Variant_lazada_upload.children = response.product_variant.children;
                    }
                    else
                    {
                        Variant_lazada_upload.variants = [];
                        Variant_lazada_upload.children = [];
                    }

                })
                .fail(function() {
                    console.log("error");
                });

            },
            getModelValue: function(index, attribute){
                if(attribute == 'name')
                    return this.item.post_title;

                if(attribute == 'price')
                    return this.item.price;

                if(attribute == 'package_weight')
                    return this.item.product_weight;

                if(attribute == 'short_description')
                    return this.item.post_content;

                if(attribute == 'quantity')
                    return this.item.product_stock;

                return $("[name='product_attributes["+index+"][value]']").val();
            },
            isReadOnly: function(attribute){
                let attributes = ['name', 'price', 'package_weight', 'short_description', 'quantity'];

                if(attributes.indexOf(attribute) !== -1)
                    return true;

                return false;
            },
            resetWindow: function(){
                 Object.assign(this.$data, initialState());
            },
            submit: function(event){
                event.preventDefault();
                let self = this;
                $.ajax({
                    url: $("#lazada_form").attr('data-action'),
                    type: 'POST',
                    data: $("#lazada_form").serialize(),
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                    }
                })
                .done(function() {
                    self.errors = [];
                    $("#reload-datatable").click();
                    $("#modal-lazada-upload").modal('hide');
                })
                .fail(function(response) {
                    self.errors = response.responseJSON.errors;
                });
                
            }
        }
    });

    window.LazadaUpload = LazadaUpload;
    
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
        $("#modal-lazada-upload").on("hidden.bs.modal", function(e){
            LazadaUpload.resetWindow();
            Variant_lazada_upload.resetWindow();
        });

        $("#modal-lazada-upload").on('shown.bs.modal', function(){
            $.ajax({
                url: $("#lazada_form").attr('data-url-lazada-category'),
                data: {shop_id: $("[name='shop_id']").val()},
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                }
              })
              .done(function(response) {
                LazadaUpload.categories = response;
              })
              .fail(function() {
                console.log("error");
              });
       });
    });

}(jQuery));