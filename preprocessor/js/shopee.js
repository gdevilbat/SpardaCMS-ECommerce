$(document).ready(function() {

    $("#shopee-sycronize").click(function(event) {
        if(window.confirm("Apakah Anda Yakin Ingin Update Data Shopee Sesuai Ecommerce ?"))
        {
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
                    }
                }).done(function(response){
                    //window.table.ajax.reload( null, false );
                });
            });
        }
    });
    
});