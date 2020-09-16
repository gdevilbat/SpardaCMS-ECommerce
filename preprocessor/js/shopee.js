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

$(document).ready(function() {

    $("#shopee-sycronize").click(function(event) {
        $(".shopee-store").each(function(index, el) {
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
    });
    
});