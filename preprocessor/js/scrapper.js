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
    /*$(".scrapping-store").each(function(index, el) {
        axios.get($(this).attr('data-url'))
            .then((response) => {
                    if(response.status === 200) {
                        let html = response.data;
                        let cheerioJquery = cheerio.load(html);
                        let devtoList = [];
                        cheerioJquery('.rvm-price').each(function(i, elem) {
                            devtoList[i] = {
                                price: cheerioJquery(this).children('[itemprop="price"]').attr('content'),
                                status: cheerioJquery(".alert-block-not-available").length > 0 ? 'empty' : 'available'
                            }      
                        });


                        let shop_price = devtoList[0].price;
                        $(this).html(currencyFormat(shop_price) + ', <br/><span class="badge '+ (devtoList[0].status == "empty" ? "badge-dark" : "badge-info") +'">' + devtoList[0].status + '</span><br/>');

                        axios.get($('#scrapping-supplier-'+$(this).attr('data-index')).attr('data-url'))
                            .then((response) => {
                                    if(response.status === 200) {
                                        let html = response.data;
                                        let cheerioJquery = cheerio.load(html);
                                        let devtoList = [];
                                        cheerioJquery('.rvm-price').each(function(i, elem) {
                                            devtoList[i] = {
                                                price: cheerioJquery(this).children('[itemprop="price"]').attr('content'),
                                                status: cheerioJquery(".alert-block-not-available").length > 0 ? 'empty' : 'available'
                                            }      
                                        });
                                        let supllier_price = devtoList[0].price;
                                        $('#scrapping-supplier-'+$(this).attr('data-index')).html(currencyFormat(supllier_price) + '<br/><span class="text-danger">('+(shop_price-supllier_price)+')</span>,<br/><span class="badge '+(devtoList[0].status == "empty" ? "badge-dark" : "badge-info")+'">' + devtoList[0].status + '</span><br/>');
                                    }
                            }, (error) => console.log(err) );
                    }
            }, (error) => console.log(err) );
    });*/
    $(".scrapping-supplier").each(function(index, el) {
        axios.get($(this).attr('data-url'))
            .then((response) => {
                    if(response.status === 200) {
                        let html = response.data;
                        let cheerioJquery = cheerio.load(html);
                        let devtoList = [];
                        cheerioJquery('.rvm-price').each(function(i, elem) {
                            devtoList[i] = {
                                price: cheerioJquery(this).children('[itemprop="price"]').attr('content'),
                                status: cheerioJquery(".alert-block-not-available").length > 0 ? 'empty' : 'available'
                            }      
                        });


                        let supplier_price = devtoList[0].price;
                        $(this).html(currencyFormat(supplier_price) + ', <br/><span class="badge '+ (devtoList[0].status == "empty" ? "badge-dark" : "badge-info") +'">' + devtoList[0].status + '</span><br/>');

                        axios.get($('#scrapping-store-'+$(this).attr('data-index')).attr('data-url'))
                            .then((response) => {
                                    if(response.status === 200) {
                                        let html = response.data;
                                        let cheerioJquery = cheerio.load(html);
                                        let devtoList = [];
                                        cheerioJquery('.rvm-price').each(function(i, elem) {
                                            devtoList[i] = {
                                                price: cheerioJquery(this).children('[itemprop="price"]').attr('content'),
                                                status: cheerioJquery(".alert-block-not-available").length > 0 ? 'empty' : 'available'
                                            }      
                                        });
                                        let store_price = devtoList[0].price;
                                        $('#scrapping-store-'+$(this).attr('data-index')).html(currencyFormat(store_price) + '<br/><span class="text-danger">('+(store_price - supplier_price)+')</span>,<br/><span class="badge '+(devtoList[0].status == "empty" ? "badge-dark" : "badge-info")+'">' + devtoList[0].status + '</span><br/>');
                                    }
                            }, (error) => console.log(err) );
                    }
            }, (error) => console.log(err) );       
    });
}

function currencyFormat(num) {
  return 'Rp. ' + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}
