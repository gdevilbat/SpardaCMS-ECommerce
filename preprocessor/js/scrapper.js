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

    $("#shopee-sycronize").click(function(event) {
        if(window.confirm("Apakah Anda Yakin Ingin Update Data Shopee Sesuai Ecommerce ?"))
        {
            let counter_shopee_sycronize = 0;
            $(".data-checklist:checked").each(function(index, el) {
                let post_id = $(this).attr('data-index');
                $.ajax({
                    url: $("#shopee-sycronize").attr('data-url-update'),
                    type: 'POST',
                    data: {'shop_id': $('#shopee-store-'+$(this).attr('data-index')).attr('data-merchant'), 'product_id': $('#shopee-store-'+$(this).attr('data-index')).attr('data-product'), 'post_id': post_id},
                    headers: {
                        "Accept": "application/json",
                        "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                    }
                }).done(function(response){
                    counter_shopee_sycronize++;
                    if(counter_shopee_sycronize >= $(".data-checklist:checked").length)
                        $("#reload-datatable").click();
                });
            });
        }
    });

});

window.tokopediaScrap = function(){
    $(".scrapping-supplier").each(function(index, el) {
        let self = this;
        let settings;

        if($("[name='scrapping[suplier_sync]']:checked").val() == 'cloud')
        {
            settings = {
              "url": $("#data_product").attr('data-url-scrapping-tokopedia-product-detail'),
              "method": "POST",
               "headers": {
    		    "Accept": "application/json",
                "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
    		  },
    		  "data": {merchant: $(this).attr('data-merchant'), slug: $(this).attr('data-slug')}
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
              "data": "[\n    {\n        \"operationName\": \"PDPInfoQuery\",\n        \"variables\": {\n            \"shopDomain\": \""+$(this).attr('data-merchant')+"\",\n            \"productKey\": \""+$(this).attr('data-slug')+"\"\n        },\n        \"query\": \"query PDPInfoQuery($shopDomain: String, $productKey: String) {\\n  getPDPInfo(productID: 0, shopDomain: $shopDomain, productKey: $productKey) {\\n    basic {\\n      id\\n      shopID\\n      name\\n      alias\\n      price\\n      priceCurrency\\n      lastUpdatePrice\\n      description\\n      minOrder\\n      maxOrder\\n      status\\n      weight\\n      weightUnit\\n      condition\\n      url\\n      sku\\n      gtin\\n      isKreasiLokal\\n      isMustInsurance\\n      isEligibleCOD\\n      isLeasing\\n      catalogID\\n      needPrescription\\n      __typename\\n    }\\n    category {\\n      id\\n      name\\n      title\\n      breadcrumbURL\\n      isAdult\\n      detail {\\n        id\\n        name\\n        breadcrumbURL\\n        __typename\\n      }\\n      __typename\\n    }\\n    pictures {\\n      picID\\n      fileName\\n      filePath\\n      description\\n      isFromIG\\n      width\\n      height\\n      urlOriginal\\n      urlThumbnail\\n      url300\\n      status\\n      __typename\\n    }\\n    preorder {\\n      isActive\\n      duration\\n      timeUnit\\n      __typename\\n    }\\n    wholesale {\\n      minQty\\n      price\\n      __typename\\n    }\\n    videos {\\n      source\\n      url\\n      __typename\\n    }\\n    campaign {\\n      campaignID\\n      campaignType\\n      campaignTypeName\\n      originalPrice\\n      discountedPrice\\n      isAppsOnly\\n      isActive\\n      percentageAmount\\n      stock\\n      originalStock\\n      startDate\\n      endDate\\n      endDateUnix\\n      appLinks\\n      hideGimmick\\n      __typename\\n    }\\n    stats {\\n      countView\\n      countReview\\n      countTalk\\n      rating\\n      __typename\\n    }\\n    txStats {\\n      txSuccess\\n      txReject\\n      itemSold\\n      __typename\\n    }\\n    cashback {\\n      percentage\\n      __typename\\n    }\\n    variant {\\n      parentID\\n      isVariant\\n      __typename\\n    }\\n    stock {\\n      useStock\\n      value\\n      stockWording\\n      __typename\\n    }\\n    menu {\\n      name\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\"\n    }\n]",
            };
        }

        $.ajax(settings).done(function (response) {

            if(response.errors == null)
            {
                let supplier_status;
                let supplier_price;
                let child_supplier = [];
                let supplier_discount;

                if(response.data.pdpGetLayout.components[3].data[0].campaign.discountedPrice > 0)
                {
                    supplier_price = response.data.pdpGetLayout.components[3].data[0].campaign.discountedPrice;
                    supplier_discount = true;
                }
                else
                {
                    supplier_price = response.data.pdpGetLayout.components[3].data[0].price.value;
                    supplier_discount = false;
                }

                let suplier_weight = response.data.pdpGetLayout.basicInfo.weight;
                $('#web-price-'+$(self).attr('data-index')).append('<br/><span class="text-danger">('+($('#web-price-'+$(self).attr('data-index')).attr('data-price') - supplier_price)+')</span>');

                if(response.data.pdpGetLayout.components[3].data[0].variant.isVariant)
                {
                    $.each(response.data.pdpGetLayout.components[2].data[0].sorted_children_by_name, function(index, val) {
                        child_supplier[index] = {
                            price: val.campaignInfo.discountPrice > 0 ? val.campaignInfo.discountPrice : val.price,
                            status: val.stock.isBuyable ? 'available' : 'empty',
                            is_discount: val.campaignInfo.discountPrice > 0 ? true : false
                        }

                        $(self).append('<div class="mb-3"><span>'+ val.name + '</span><br/>' + currencyFormat(child_supplier[index]['price']) + (child_supplier[index].is_discount ? ' - <i class="fab fa-hotjar"></i>' : '') +', <br/><span class="badge '+ (child_supplier[index]['status'] == "empty" ? "badge-dark" : "badge-info") +'">' + child_supplier[index]['status'] + '</span></div>'); 
                    });
                }
                else
                {
                    supplier_status = response.data.pdpGetLayout.basicInfo.status== "ACTIVE" && response.data.pdpGetLayout.components[3].data[0].stock.useStock ? 'available' : 'empty';
                    $(self).append('<div class="mb-3">' + currencyFormat(supplier_price) + (supplier_discount ? ' - <i class="fab fa-hotjar"></i>' : '') +', <br/><span class="badge '+ (supplier_status == "empty" ? "badge-dark" : "badge-info") +'">' + supplier_status + '</span></div>');
                }

                if(response.data.pdpGetLayout.components[3].data[0].preorder.isActive)
                {
                    $(self).append('<div class="mb-3"> <span class="badge badge-secondary">preorder</span></div>');
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
                =            Shopee            =
                =============================================*/
                
                    if($('#shopee-store-'+$(self).attr('data-index')).attr('data-merchant'))
                    {
                        let config_shopee;

                        if($("[name='scrapping[store_sync]']:checked").val() == 'cloud')
                        {
                            config_shopee = {
                              method: 'post',
                              url: $("#data_product").attr('data-url-scrapping-shopee'),
                              headers: {
                                "Accept": "application/json",
                                "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content') 
                              },
                              data: {'shopid': $('#shopee-store-'+$(self).attr('data-index')).attr('data-merchant'), 'itemid': $('#shopee-store-'+$(self).attr('data-index')).attr('data-product')}
                            };
                        }
                        else
                        {
                            config_shopee = {
                              url: 'https://shopee.co.id/api/v2/item/get'+'?itemid='+$('#shopee-store-'+$(self).attr('data-index')).attr('data-product')+'&shopid='+$('#shopee-store-'+$(self).attr('data-index')).attr('data-merchant'),
                            };
                        }

                        axios(config_shopee)
                            .then((response) => {
                                    if(response.status === 200) {
                                        let html = response.data;
                                        let devtoList = [];
                                        devtoList[0] = {
                                            price: (html.item.price)/100000,
                                            status: html.item.stock > 0 ? 'available' : 'empty',
                                            is_discount: html.item.price_before_discount > 0 ? true : false
                                        }

                                        if(html.item.models[0].name == '')
                                        {
                                            $('#shopee-store-'+$(self).attr('data-index')).append(currencyFormat(devtoList[0].price) + (devtoList[0].is_discount ? ' - <i class="fab fa-hotjar"></i>' : '') + '<br/><span class="text-danger">('+(devtoList[0].price - supplier_price)+')</span>,<br/><span class="badge '+(devtoList[0].status == "empty" ? "badge-dark" : "badge-info")+'">' + devtoList[0].status + '</span><br/>');
                                        }
                                        else
                                        {
                                            let child_shopee_store = [];
                                            $.each(html.item.sorted_models_by_name, function(index, val) {
                                                 child_shopee_store[index] = {
                                                    price: val.price/100000,
                                                    status: val.stock > 0 ? 'available' : 'empty',
                                                    is_discount: val.price_before_discount > 0 ? true : false
                                                 }

                                                if(child_supplier[index] != undefined)
                                                {
                                                    $('#shopee-store-'+$(self).attr('data-index')).append('<div class="mb-3"><span>' + val.name + '<span><br/>' + currencyFormat(child_shopee_store[index].price) + (child_shopee_store[index].is_discount ? ' - <i class="fab fa-hotjar"></i>' : '') +'<br/><span class="text-danger">('+(child_shopee_store[index].price - child_supplier[index].price)+')</span>,<br/><span class="badge '+(child_shopee_store[index].status == "empty" ? "badge-dark" : "badge-info")+'">' + child_shopee_store[index].status + '</span></div>');
                                                }
                                                else
                                                {
                                                    $('#shopee-store-'+$(self).attr('data-index')).append('<div class="mb-3"><span>' + val.name + '<span><br/>' + currencyFormat(child_shopee_store[index].price) + (child_shopee_store[index].is_discount ? ' - <i class="fab fa-hotjar"></i>' : '') +'<br/><span class="badge '+(child_shopee_store[index].status == "empty" ? "badge-dark" : "badge-info")+'">' + child_shopee_store[index].status + '</span></div>');
                                                }
                                            });
                                        }

                                        if(html.item.is_pre_order)
                                        {
                                            $('#shopee-store-'+$(self).attr('data-index')).append('<div class="mb-3"><span class="badge badge-secondary">preorder</span></div>');
                                        }

                                    }
                            }, (error) => console.log(err) );

                        let config_detail = {
                          method: 'post',
                          url: $("#data_product").attr('data-url-shopee-detail'),
                          headers: {
                            "Accept": "application/json",
                            "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content') 
                          },
                          data: {'shop_id': $('#shopee-store-'+$(self).attr('data-index')).attr('data-merchant'), 'product_id': $('#shopee-store-'+$(self).attr('data-index')).attr('data-product')}
                        };

                        if($("[name='scrapping[weight_check]']").val() == 'true')
                        {
                            axios(config_detail)
                                .then((response) => {
                                        if(response.status === 200) {
                                            let shopee_store_weight = parseInt(response.data.weight)
                                            if(shopee_store_weight != suplier_weight)
                                                $('#shopee-weight-'+$(self).attr('data-index')).html('<hr>('+(shopee_store_weight - suplier_weight)+')<br/>');

                                        }
                                }, (error) => console.log(err) );
                        }

                    }
                
                /*=====  End of Shopee  ======*/

                /*=================================
                =            Lazada            =
                =================================*/

                    if($('#lazada-store-'+$(self).attr('data-index')).attr('data-merchant') != undefined)
                    {
                        let config_lazada = {
                              method: 'post',
                              url: $("#data_product").attr('data-url-scrapping-lazada'),
                              headers: {
                                "Accept": "application/json",
                                "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content') 
                              },
                              data: {'product_id': $('#lazada-store-'+$(self).attr('data-index')).attr('data-product')}
                            };

                        axios(config_lazada)
                            .then((response) => {
                                    if(response.status === 200) {
                                        let data = response.data;
                                        let child_lazada_store = [];

                                        if(data.skuInfosSortByName[0].name == 'Random,')
                                        {
                                            child_lazada_store = {
                                                'price' : data.skuInfosSortByName[0].price.salePrice.value,
                                                'status': data.skuInfosSortByName[0].stock > 0 ? 'available' : 'empty'
                                            }
                                            $('#lazada-store-'+$(self).attr('data-index')).html(currencyFormat(child_lazada_store.price) + '<br/><span class="text-danger">('+(child_lazada_store.price - supplier_price)+')</span>,<br/><span class="badge '+(child_lazada_store.status == "empty" ? "badge-dark" : "badge-info")+'">' + child_lazada_store.status + '</span><br/>');
                                        }
                                        else
                                        {
                                            $.each(data.skuInfosSortByName, function(index, val) {
                                                 let price = val.price.salePrice.value;
                                                 let status =  val.stock > 0 ? 'available' : 'empty';

                                                 child_lazada_store[index] = {
                                                    price: price,
                                                    status: status
                                                 }

                                                if(child_supplier[index] != undefined)
                                                {
                                                    $('#lazada-store-'+$(self).attr('data-index')).append('<div class="mb-3"><span>' + val.name + '<span><br/>' + currencyFormat(child_lazada_store[index].price) + '<br/><span class="text-danger">('+(child_lazada_store[index].price - child_supplier[index].price)+')</span>,<br/><span class="badge '+(child_lazada_store[index].status == "empty" ? "badge-dark" : "badge-info")+'">' + child_lazada_store[index].status + '</span></div>');
                                                }
                                                else
                                                {
                                                    $('#lazada-store-'+$(self).attr('data-index')).append('<div class="mb-3"><span>' + val.name + '<span><br/>' + currencyFormat(child_lazada_store[index].price) + '<br/><span class="badge '+(child_lazada_store[index].status == "empty" ? "badge-dark" : "badge-info")+'">' + child_lazada_store[index].status + '</span></div>');
                                                }
                                            });
                                        }                                        
                                    }
                            }, (error) => console.log(error) );
                    }
                
                
                /*=====  End of Lazada  ======*/

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

function findTextAndReturnRemainder(target, variable, endString){
    var chopFront = target.substring(target.search(variable)+variable.length,target.length);
    var result = chopFront.substring(0,chopFront.search(endString));
    return result;
}

window.popupWindow = function(url, windowName, win, w, h) {
    const y = win.top.outerHeight / 2 + win.top.screenY - ( h / 2);
    const x = win.top.outerWidth / 2 + win.top.screenX - ( w / 2);
    return win.open(url, windowName, `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=${w}, height=${h}, top=${y}, left=${x}`);
}
