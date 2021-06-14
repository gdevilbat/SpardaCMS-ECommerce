var data_scrapping = new Vue({
    el: "#data_scrapping",
    data: {
        items: [],
    },
    methods:{
      setShopeeUploadItem: function(item){
          shopee_upload.item = item;
          $('#modal-shopee-upload').modal('show');
      }
    } 
});

$(document).ready(function() {
    $("#submit-data-scrapping").click(function(event) {
        var settings = {
          "url": $("#data-web-scrapping").attr('action'),
          "method": "POST",
          "headers": {
            "accept": "application/json",
            "Authorization": "Bearer "+ $("[name='scrapping[token]']").val()
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