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
          "url": "https://gql.tokopedia.com/",
          "method": "POST",
          "data": "[\n    {\n        \"operationName\": \"PDPInfoQuery\",\n        \"variables\": {\n            \"shopDomain\": \""+$(this).attr('data-shop-domain')+"\",\n            \"productKey\": \""+$(this).attr('data-product-key')+"\"\n        },\n        \"query\": \"query PDPInfoQuery($shopDomain: String, $productKey: String) {\\n  getPDPInfo(productID: 0, shopDomain: $shopDomain, productKey: $productKey) {\\n    basic {\\n      id\\n      shopID\\n      name\\n      alias\\n      price\\n      priceCurrency\\n      lastUpdatePrice\\n      description\\n      minOrder\\n      maxOrder\\n      status\\n      weight\\n      weightUnit\\n      condition\\n      url\\n      sku\\n      gtin\\n      isKreasiLokal\\n      isMustInsurance\\n      isEligibleCOD\\n      isLeasing\\n      catalogID\\n      needPrescription\\n      __typename\\n    }\\n    category {\\n      id\\n      name\\n      title\\n      breadcrumbURL\\n      isAdult\\n      detail {\\n        id\\n        name\\n        breadcrumbURL\\n        __typename\\n      }\\n      __typename\\n    }\\n    pictures {\\n      picID\\n      fileName\\n      filePath\\n      description\\n      isFromIG\\n      width\\n      height\\n      urlOriginal\\n      urlThumbnail\\n      url300\\n      status\\n      __typename\\n    }\\n    preorder {\\n      isActive\\n      duration\\n      timeUnit\\n      __typename\\n    }\\n    wholesale {\\n      minQty\\n      price\\n      __typename\\n    }\\n    videos {\\n      source\\n      url\\n      __typename\\n    }\\n    campaign {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      originalPrice\\n      discountedPrice\\n      isAppsOnly\\n      isActive\\n      percentageAmount\\n      stock\\n      originalStock\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      hideGimmick\\n      __typename\\n    }\\n    stats {\\n      countView\\n      countReview\\n      countTalk\\n      rating\\n      __typename\\n    }\\n    txStats {\\n      txSuccess\\n      txReject\\n      itemSold\\n      __typename\\n    }\\n    cashback {\\n      percentage\\n      __typename\\n    }\\n    variant {\\n      parentID\\n      isVariant\\n      __typename\\n    }\\n    stock {\\n      useStock\\n      value\\n      stockWording\\n      __typename\\n    }\\n    menu {\\n      name\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"\n    }\n]",
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
                      "url": "https://gql.tokopedia.com/",
                      "method": "POST",
                      "data": "[\r\n    {\r\n        \"operationName\": \"ProductVariantQuery\",\r\n        \"variables\": {\r\n            \"productID\": \""+response[0].data.getPDPInfo.basic.id+"\",\r\n            \"includeCampaign\": true\r\n        },\r\n        \"query\": \"query ProductVariantQuery($productID: String!, $includeCampaign: Boolean!) {\\n  getProductVariant(productID: $productID, option: {userID: \\\"0\\\", includeCampaign: $includeCampaign}) {\\n    parentID\\n    defaultChild\\n    variant {\\n      productVariantID\\n      variantID\\n      variantUnitID\\n      name\\n      identifier\\n      unitName\\n      position\\n      option {\\n        productVariantOptionID\\n        variantUnitValueID\\n        value\\n        hex\\n        picture {\\n          urlOriginal: url\\n          urlThumbnail: url200\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    children {\\n      productID\\n      price\\n      priceFmt\\n      sku\\n      optionID\\n      productName\\n      productURL\\n      picture {\\n        urlOriginal: url\\n        urlThumbnail: url200\\n        __typename\\n      }\\n      stock {\\n        stock\\n        isBuyable\\n        alwaysAvailable\\n        isLimitedStock\\n        stockWording\\n        stockWordingHTML\\n        otherVariantStock\\n        minimumOrder\\n        maximumOrder\\n        __typename\\n      }\\n      isCOD\\n      isWishlist\\n      campaignInfo {\\n        stock\\n        originalStock\\n        endDateUnix\\n        isActive\\n        appLinks\\n        startDate\\n        campaignID\\n        isAppsOnly\\n        campaignType\\n        originalPrice\\n        discountPrice\\n        originalPriceFmt\\n        discountPriceFmt\\n        campaignTypeName\\n        discountPercentage\\n        hideGimmick\\n        __typename\\n      }\\n      __typename\\n    }\\n    sizeChart\\n    enabled\\n    alwaysAvailable\\n    stock\\n    __typename\\n  }\\n}\\n\"\r\n    }\r\n]",
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
                    if(response[0].data.getPDPInfo.stock.value == 999999 || response[0].data.getPDPInfo.stock.value == 0)
                    {
                        supplier_status = 'empty';
                    }
                    else
                    {
                        supplier_status = 'available';
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
