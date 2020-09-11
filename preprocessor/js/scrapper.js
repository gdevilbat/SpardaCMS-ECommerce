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

$("#syncronize_ecommerce").change(function(event) {
    if ($(this).is(':checked'))
    {
        $("[name='syncronize_ecommerce']").attr('value', 'true');
    }
    else
    {
        $("[name='syncronize_ecommerce']").attr('value', 'false');
    }

    $(this).parents("form").eq(0).submit();
});

$("#weight_check").change(function(event) {
    if ($(this).is(':checked'))
    {
        $("[name='scrapping[weight_check]']").attr('value', 'true');
    }
    else
    {
        $("[name='scrapping[weight_check]']").attr('value', 'false');
    }
});

window.tokopediaScrap = function(){
    $(".scrapping-supplier").each(function(index, el) {
        let self = this;
        let settings;

        if($("[name='scrapping[suplier_sync]']:checked").val() == 'cloud')
        {
            settings = {
              "url": $("#data-product").attr('data-url-scrapping-product'),
              "method": "POST",
               "headers": {
    		    "Accept": "application/json",
                "Authorization": "Bearer "+ $("[name='scrapping[token]']").val()
    		  },
    		  "data": {domain: $(this).attr('data-shop-domain'), productKey: $(this).attr('data-product-key')}
            };
        }
        else
        {
            settings = {
              "url": "https://gql.tokopedia.com/",
              "method": "POST",
              "headers": {
                "content-type": "application/json",
              },
              "data": "[\n    {\n        \"operationName\": \"PDPInfoQuery\",\n        \"variables\": {\n            \"shopDomain\": \""+$(this).attr('data-shop-domain')+"\",\n            \"productKey\": \""+$(this).attr('data-product-key')+"\"\n        },\n        \"query\": \"query PDPInfoQuery($shopDomain: String, $productKey: String) {\\n  getPDPInfo(productID: 0, shopDomain: $shopDomain, productKey: $productKey) {\\n    basic {\\n      id\\n      shopID\\n      name\\n      alias\\n      price\\n      priceCurrency\\n      lastUpdatePrice\\n      description\\n      minOrder\\n      maxOrder\\n      status\\n      weight\\n      weightUnit\\n      condition\\n      url\\n      sku\\n      gtin\\n      isKreasiLokal\\n      isMustInsurance\\n      isEligibleCOD\\n      isLeasing\\n      catalogID\\n      needPrescription\\n      __typename\\n    }\\n    category {\\n      id\\n      name\\n      title\\n      breadcrumbURL\\n      isAdult\\n      detail {\\n        id\\n        name\\n        breadcrumbURL\\n        __typename\\n      }\\n      __typename\\n    }\\n    pictures {\\n      picID\\n      fileName\\n      filePath\\n      description\\n      isFromIG\\n      width\\n      height\\n      urlOriginal\\n      urlThumbnail\\n      url300\\n      status\\n      __typename\\n    }\\n    preorder {\\n      isActive\\n      duration\\n      timeUnit\\n      __typename\\n    }\\n    wholesale {\\n      minQty\\n      price\\n      __typename\\n    }\\n    videos {\\n      source\\n      url\\n      __typename\\n    }\\n    campaign {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      originalPrice\\n      discountedPrice\\n      isAppsOnly\\n      isActive\\n      percentageAmount\\n      stock\\n      originalStock\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      hideGimmick\\n      __typename\\n    }\\n    stats {\\n      countView\\n      countReview\\n      countTalk\\n      rating\\n      __typename\\n    }\\n    txStats {\\n      txSuccess\\n      txReject\\n      itemSold\\n      __typename\\n    }\\n    cashback {\\n      percentage\\n      __typename\\n    }\\n    variant {\\n      parentID\\n      isVariant\\n      __typename\\n    }\\n    stock {\\n      useStock\\n      value\\n      stockWording\\n      __typename\\n    }\\n    menu {\\n      name\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"\n    }\n]",
            };
        }

        $.ajax(settings).done(function (response) {

            if(response[0].data != null)
            {
                let supplier_status;
                let supplier_price = response[0].data.getPDPInfo.basic.price;
                let suplier_weight = response[0].data.getPDPInfo.basic.weight;
                $('#web-price-'+$(self).attr('data-index')).append('<br/><span class="text-danger">('+($('#web-price-'+$(self).attr('data-index')).attr('data-price') - supplier_price)+')</span>');

                if(response[0].data.getPDPInfo.variant.isVariant)
                {
                    let settings;

                    if($("[name='scrapping[suplier_sync]']:checked").val() == 'cloud')
                    {
                        settings = {
                          "url": $("#data-product").attr('data-url-scrapping-variant'),
                          "method": "POST",
                          "headers": {
    					    "Accept": "application/json",
                            "Authorization": "Bearer "+ $("[name='scrapping[token]']").val()
    					  },
    					  "data": {variant_id: response[0].data.getPDPInfo.basic.id}
                        };
                    }
                    else
                    {
                        settings = {
                          "url": "https://gql.tokopedia.com/",
                          "method": "POST",
                          "headers": {
                            "content-type": "application/json",
                          },
                          "data": "[\r\n    {\r\n        \"operationName\": \"ProductVariantQuery\",\r\n        \"variables\": {\r\n            \"productID\": \""+response[0].data.getPDPInfo.basic.id+"\",\r\n            \"includeCampaign\": true\r\n        },\r\n        \"query\": \"query ProductVariantQuery($productID: String!, $includeCampaign: Boolean!) {\\n  getProductVariant(productID: $productID, option: {userID: \\\"0\\\", includeCampaign: $includeCampaign}) {\\n    parentID\\n    defaultChild\\n    variant {\\n      productVariantID\\n      variantID\\n      variantUnitID\\n      name\\n      identifier\\n      unitName\\n      position\\n      option {\\n        productVariantOptionID\\n        variantUnitValueID\\n        value\\n        hex\\n        picture {\\n          urlOriginal: url\\n          urlThumbnail: url200\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    children {\\n      productID\\n      price\\n      priceFmt\\n      sku\\n      optionID\\n      productName\\n      productURL\\n      picture {\\n        urlOriginal: url\\n        urlThumbnail: url200\\n        __typename\\n      }\\n      stock {\\n        stock\\n        isBuyable\\n        alwaysAvailable\\n        isLimitedStock\\n        stockWording\\n        stockWordingHTML\\n        otherVariantStock\\n        minimumOrder\\n        maximumOrder\\n        __typename\\n      }\\n      isCOD\\n      isWishlist\\n      campaignInfo {\\n        stock\\n        originalStock\\n        endDateUnix\\n        isActive\\n        appLinks\\n        startDate\\n        campaignID\\n        isAppsOnly\\n        campaignType\\n        originalPrice\\n        discountPrice\\n        originalPriceFmt\\n        discountPriceFmt\\n        campaignTypeName\\n        discountPercentage\\n        hideGimmick\\n        __typename\\n      }\\n      __typename\\n    }\\n    sizeChart\\n    enabled\\n    alwaysAvailable\\n    stock\\n    __typename\\n  }\\n}\\n\"\r\n    }\r\n]",
                        };
                    }

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

                        let api_url;

                        let config;

                        if($("[name='scrapping[store_sync]']:checked").val() == 'cloud')
                        {
                            config = {
                              method: 'post',
                              url: $("#data-product").attr('data-url-scrapping-shopee')+'?shopid='+shopee[0]+'&itemid='+shopee[1],
                              headers: {
                                "Accept": "application/json",
                                "Authorization": "Bearer "+ $("[name='scrapping[token]']").val() 
                              }
                            };
                        }
                        else
                        {
                            config = {
                              url: 'https://shopee.co.id/api/v2/item/get'+'?shopid='+shopee[0]+'&itemid='+shopee[1],
                            };
                        }

                        axios(config)
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

                        /*let config = {
                          method: 'get',
                          url: 'https://seller.shopee.co.id/api/v3/product/get_product_detail/?product_id='+shopee[1],
                          headers: { 
                            'cookie': 'fbm_957549474255294=base_domain=.shopee.co.id; cto_lwid=195b1764-0ef0-4bec-aa92-ac4a603bf9df; __utma=156485241.1231309772.1569496850.1571747440.1571747440.1; SPC_F=fEh1EGf8MiXYqPtpJU3Y4wQTKYud6itt; _gcl_au=1.1.2058148069.1591685537; _fbp=fb.2.1591685537834.1900528078; SC_DFP=L3uPlm1FDKc30axfxqaCuaC3rxO79u1I; G_ENABLED_IDPS=google; SPC_CDS=51f3ab59-1df7-4ae0-b9d8-2bce7d03ca4c; UYOMAPJWEMDGJ=; SPC_SC_SA_TK=; SPC_SC_SA_UD=; SPC_SC_TK=92229a9f904347e62e3685971bfb8964; SPC_WST="d+WJMbfdAZxq8o6f1ZZdUFSKsQVVfK+WLPrrKJbwUGW7HJP6BvMzLMvreoPN/5T71XzIKkv+cSdwm7rMWPOLv/QT5xuzNf7hj3gdzNg7CuW/YskfPO+S3KvHDyUMc3IdWQ1bCEgzpz3wI0cgqizcFsznxnC1HmZk4rnQZSZo+Vc="; SPC_SC_UD=89948237; SPC_U=89948237; SPC_EC=d+WJMbfdAZxq8o6f1ZZdUFSKsQVVfK+WLPrrKJbwUGW7HJP6BvMzLMvreoPN/5T71XzIKkv+cSdwm7rMWPOLv/QT5xuzNf7hj3gdzNg7CuW/YskfPO+S3KvHDyUMc3IdWQ1bCEgzpz3wI0cgqizcFsznxnC1HmZk4rnQZSZo+Vc=; AMP_TOKEN=%24NOT_FOUND; _gid=GA1.3.596009003.1598518667; SPC_SI=3abmbd01glvqb7yk94y6knoj6wlia9tq; _med=refer; _ga=GA1.1.1231309772.1569496850; _ga_SW6D8G0HXK=GS1.1.1598518666.5.1.1598520168.0; SPC_R_T_ID="qGo1kOmHqovN6BRYoVfDJfBnOcyr010+Xd4t6vVF41H7aZ49kfg4Zl4YWjKFO9pau2YxuUCORqb4EqaoT1S+sOiWAMpRqw9c6sxGy5LluII="; SPC_R_T_IV="HMPB0wAL55/voizsjYIyqQ=="'
                          }
                        };*/

                        let config_detail = {
                          method: 'post',
                          url: $("#data-product").attr('data-url-shopee-detail')+'?product_id='+shopee[1],
                          headers: {
                            "Accept": "application/json",
                            "Authorization": "Bearer "+ $("[name='scrapping[token]']").val() 
                          }
                        };

                        if($("[name='scrapping[weight_check]']").val() == 'true')
                        {
                            axios(config_detail)
                                .then((response) => {
                                        if(response.status === 200) {
                                            let shopee_store_weight = parseInt(response.data.data.weight)
                                            if(shopee_store_weight != suplier_weight)
                                                $('#shopee-weight-'+$(self).attr('data-index')).html('<hr>('+(shopee_store_weight - suplier_weight)+')<br/>');

                                        }
                                }, (error) => console.log(err) );
                        }

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
