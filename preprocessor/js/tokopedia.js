(function($){
  var DataScrapping = new Vue({
      el: "#data_scrapping",
      data: {
          items: [],
      },
      methods:{
        setTokopediaDownloadItem: function(item){
            TokopediaDownload.item = item;
            $.ajax({
              url: $('#data-product').attr('data-url-scrapping-tokopedia-product-detail'),
              method: "POST",
              headers: {
                "Accept": "application/json",
                    "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
              },
              data: {merchant: item.store, slug: item.slug}
            })
            .done(function(response) {
              TokopediaDownload.$set(TokopediaDownload.item, 'product_id', response[0].data.pdpGetLayout.basicInfo.id);

              let images = response[0].data.pdpGetLayout.components[0].data[0].media;

              $.each(images, function(index, val) {
                 images[index].urlOriginal = val.prefix+'700'+val.suffix;
              });

              TokopediaDownload.$set(TokopediaDownload.item, 'images', images);

              $.each(response[0].data.pdpGetLayout.components[4].data[0].content, function(index, el) {
                if(el.title == 'Deskripsi')
                  TokopediaDownload.$set(TokopediaDownload.item, 'description', el.subtitle);
              });

              TokopediaDownload.$set(TokopediaDownload.item, 'product_weight', response[0].data.pdpGetLayout.basicInfo.weight/1000);

              if(response[0].data.pdpGetLayout.components[3].data[0].campaign.discountedPrice > 0)
              {
                TokopediaDownload.$set(TokopediaDownload.item, 'price', response[0].data.pdpGetLayout.components[3].data[0].campaign.discountedPrice);
              }
              else
              {
                TokopediaDownload.$set(TokopediaDownload.item, 'price', response[0].data.pdpGetLayout.components[3].data[0].price.value);
              }

              TokopediaDownload.$set(TokopediaDownload.item, 'is_variant', response[0].data.pdpGetLayout.components[3].data[0].variant.isVariant);
              TokopediaDownload.$set(TokopediaDownload.item, 'condition', response[0].data.pdpGetLayout.basicInfo.condition.toLowerCase());

              if(response[0].data.pdpGetLayout.components[3].data[0].variant.isVariant)
              {
                TokopediaDownload.$set(TokopediaDownload.item, 'children', response[0].data.pdpGetLayout.components[2].data[0].children);
                TokopediaDownload.$set(Variant_tokopedia_download, 'children', response[0].data.pdpGetLayout.components[2].data[0].sorted_children_by_option_id);
                TokopediaDownload.$set(Variant_tokopedia_download, 'variants', response[0].data.pdpGetLayout.components[2].data[0].variants);
              }
            })
            .fail(function() {
              console.log("error");
            });
            
            $('#modal-tokopedia-download').modal('show');
        },
      } 
  });

  $(document).ready(function() {
      $("#submit-data-scrapping").click(function(event) {
          var settings = {
            "url": $("#data-web-scrapping").attr('action'),
            "method": "POST",
            "headers": {
              "accept": "application/json",
              "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
            },
            "data": $("#data-web-scrapping").serialize()
          };

          $.ajax(settings).done(function (response) {
            DataScrapping.items = response.list;
          }).fail(function(response){
              alert(JSON.stringify(response.responseJSON.errors));
          });
      });
  });

   var TokopediaDownload = new Vue({
        el: "#tokopedia_download",
        data: function (){
            return initialState();
        }, 
        methods: {
          resetWindow: function(){
               Object.assign(this.$data, initialState());
          },
          removeChildren: function(index){
            this.item.children.splice(index, 1);
          },
          removeImage: function(index){
            this.item.images.splice(index, 1);
          },
          addImage: function(){
            this.item.images.push({urlOriginal: ''});
          },
          addChildren: function(){
            this.item.children.push({product_id: ''});
          },
          submit: function(event){
              event.preventDefault();
              self = this;
              $.ajax({
                  url: $("#tokopedia_form").attr('data-action'),
                  type: 'POST',
                  data: $("#tokopedia_form").serialize(),
                  headers: {
                      "Accept": "application/json",
                      "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                  }
              })
              .done(function() {
                  self.errors = [];
                  $("#reload-datatable").click();
                  $("#modal-tokopedia-download").modal('hide');
                  $("#submit-data-scrapping").click();
              })
              .fail(function(response) {
                  self.errors = response.responseJSON.errors;
              });
              
          }
        },
    });

    window.TokopediaDownload = TokopediaDownload;

    function initialState (){
      return {
        item: [],
        errors: [],
        product_sale: null,
        product_weight: null,
        product_avalability: null,
        product_stock: null,
      }
    }

    $(document).ready(function() {
      $("#modal-tokopedia-download").on("hidden.bs.modal", function(e){
          TokopediaDownload.resetWindow();
          Variant_tokopedia_download.resetWindow();
      });
    });

}(jQuery));
