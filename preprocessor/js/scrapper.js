/*require('colors');
const request = require('request');

let cheerio = require('cheerio');
let jsonframe = require('jsonframe-cheerio');

let $ = cheerio.load('https://www.tokopedia.com/sparda-store/acer-nitro-5-an515-52-51t2-i5-8300h-8gb-1tb-gtx1050-4gb-w10');
jsonframe($); // initializes the plugin

var frame = {
	"companies": {           // setting the parent item as "companies"
		"selector": "rvm-price-holder",    // defines the elements to search for
		"data": [{              // "data": [{}] defines a list of items
			"price": ".rvm-price [itemprop=price]",         // inline selector defining "name" so "company"."name"
		}]
	}

};

var companiesList = $('.rvm-pdp-product').scrape(frame);
console.log(companiesList); // Output the data in the terminal*/

let axios = require('axios');
let cheerio = require('cheerio');
//let fs = require('file-system');
//

window.tokopediaScrap = function(){
    $(".scrapping-supplier").each(function(index, el) {
        let self = this;
        let settings = {
          "url": $("#data-product").attr('data-url-scrapping-product'),
           "headers": {
		    "content-type": "application/json",
		  },
		  "data": {domain: $(this).attr('data-shop-domain'), productKey: $(this).attr('data-product-key')}
        };

        $.ajax(settings).done(function (response) {

            if(response[0].data != null)
            {
                let supplier_status;
                let supplier_price = response[0].data.getPDPInfo.basic.price;
                $('#web-price-'+$(self).attr('data-index')).append('<br/><span class="text-danger">('+($('#web-price-'+$(self).attr('data-index')).attr('data-price') - supplier_price)+')</span>');

                if(response[0].data.getPDPInfo.variant.isVariant)
                {
                    let settings = {
                      "url": $("#data-product").attr('data-url-scrapping-variant'),
                      "headers": {
					    "content-type": "application/json",
					  },
					  "data": {variant_id: response[0].data.getPDPInfo.basic.id}
                    };

                    $.ajax(settings).done(function (response) {
                        let status_boolean = false;
                        $.each(response[0].data.getProductVariant.children, function(index, val) {
                            if(val.stock.otherVariantStock == 'available')
                                status_boolean = true; 
                        });

                        if(status_boolean)
                        {
                            supplier_status = 'available';
                        }
                        else
                        {
                            supplier_status = 'empty';
                        }

                        $(self).html(currencyFormat(supplier_price) + ', <br/><span class="badge '+ (supplier_status == "empty" ? "badge-dark" : "badge-info") +'">' + supplier_status + '</span><br/>');
                    });
                }
                else
                {
                    if(response[0].data.getPDPInfo.basic.status == "ACTIVE")
                    {
                        supplier_status = 'available';
                    }
                    else
                    {
                        supplier_status = 'empty';
                    }

                    $(self).html(currencyFormat(supplier_price) + ', <br/><span class="badge '+ (supplier_status == "empty" ? "badge-dark" : "badge-info") +'">' + supplier_status + '</span><br/>');
                }

                /*=================================
                =            Tokopedia            =
                =================================*/
                
                    /*axios.get($('#tokopedia-store-'+$(this).attr('data-index')).attr('data-url'))
                        .then((response) => {
                                if(response.status === 200) {
                                    let html = response.data;
                                    let cheerioJquery = cheerio.load(html);
                                    let devtoList = [];

                                    devtoList[0] = {
                                        price: cheerioJquery('meta[property="product:price:amount"]').attr('content'),
                                        status: cheerioJquery('[data-merchant-test="txtPDPWarningEmptyStock"]').length > 0 ? 'empty' : 'available'
                                    }
                                    
                                    let tokopedia_store_price = devtoList[0].price;
                                    $('#tokopedia-store-'+$(this).attr('data-index')).html(currencyFormat(tokopedia_store_price) + '<br/><span class="text-danger">('+(tokopedia_store_price - supplier_price)+')</span>,<br/><span class="badge '+(devtoList[0].status == "empty" ? "badge-dark" : "badge-info")+'">' + devtoList[0].status + '</span><br/>');
                                }
                        }, (error) => console.log(err) );*/
                
                /*=====  End of Tokopedia  ======*/

                /*=============================================
                =            Shopee comment block            =
                =============================================*/
                
                    let url = $('#shopee-store-'+$(self).attr('data-index')).attr('data-url')

                    if(url != undefined)
                    {
                        let shopee = url.split('/').slice(-2);

                        axios.get('https://shopee.co.id/api/v2/item/get?shopid='+shopee[0]+'&itemid='+shopee[1])
                            .then((response) => {
                                    if(response.status === 200) {
                                        let html = response.data;
                                        let devtoList = [];
                                        devtoList[0] = {
                                            price: (html.item.price)/100000,
                                            status: html.item.stock > 0 ? 'available' : 'empty'
                                        }
                                        let shopee_store_price = devtoList[0].price;
                                        $('#shopee-store-'+$(self).attr('data-index')).html(currencyFormat(shopee_store_price) + '<br/><span class="text-danger">('+(shopee_store_price - supplier_price)+')</span>,<br/><span class="badge '+(devtoList[0].status == "empty" ? "badge-dark" : "badge-info")+'">' + devtoList[0].status + '</span><br/>');
                                    }
                            }, (error) => console.log(err) );
                    }
                
                /*=====  End of Shopee comment block  ======*/

            }
            else
            {
                $(self).html('<span class="badge badge-danger">' + "Not Found" + '</span><br/>');
            }

        });
    });
}

function currencyFormat(num) {
  return 'Rp. ' + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}
