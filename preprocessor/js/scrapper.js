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
    $(".scrapping-shop").each(function(index, el) {
        axios.get($(this).attr('data-url'))
            .then((response) => {
                    if(response.status === 200) {
                        let html = response.data;
                        let cheerioJquery = cheerio.load(html);
                        let devtoList = [];
                        cheerioJquery('.rvm-price').each(function(i, elem) {
                            devtoList[i] = {
                                price: cheerioJquery(this).children('[itemprop="price"]').attr('content')
                            }      
                        });
                        shop_price = devtoList[0].price;
                        $(this).html(currencyFormat(shop_price) + ', <br/>');

                        axios.get($('#scrapping-supplier-'+$(this).attr('data-index')).attr('data-url'))
                            .then((response) => {
                                    if(response.status === 200) {
                                        let html = response.data;
                                        let cheerioJquery = cheerio.load(html);
                                        let devtoList = [];
                                        cheerioJquery('.rvm-price').each(function(i, elem) {
                                            devtoList[i] = {
                                                price: cheerioJquery(this).children('[itemprop="price"]').attr('content')
                                            }      
                                        });
                                        supllier_price = devtoList[0].price;
                                        window.console.log(supllier_price);
                                        window.console.log( $('#scrapping-supplier-'+$(this).attr('data-index')).attr('data-url'));
                                        $('#scrapping-supplier-'+$(this).attr('data-index')).html(currencyFormat(supllier_price) + '<br/>('+(shop_price-supllier_price)+'),<br/>');
                                    }
                            }, (error) => console.log(err) );
                    }
            }, (error) => console.log(err) );
    });
}

function currencyFormat(num) {
  return 'Rp. ' + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}
