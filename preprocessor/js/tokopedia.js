(function($){
  var data_scrapping = new Vue({
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
              data: {domain: item.store, productKey: item.slug}
            })
            .done(function(response) {
              TokopediaDownload.$set(TokopediaDownload.item, 'images', response[0].data.getPDPInfo.pictures);
              TokopediaDownload.$set(TokopediaDownload.item, 'description', response[0].data.getPDPInfo.basic.description);
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
            data_scrapping.items = response.list;
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
          submit: function(){
              self = this;
              $.ajax({
                  url: $("#tokopedia_download").attr('data-action'),
                  type: 'POST',
                  data: $("#tokopedia_download").serialize(),
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
        product_avalability: null,
        product_condition: null,
      }
    }

    $(document).ready(function() {
      $("#modal-tokopedia-download").on("hidden.bs.modal", function(e){
          TokopediaDownload.resetWindow();
      });
    });

}(jQuery));
