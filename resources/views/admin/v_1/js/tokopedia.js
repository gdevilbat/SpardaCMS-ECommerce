!function(t){function e(o){if(a[o])return a[o].exports;var n=a[o]={i:o,l:!1,exports:{}};return t[o].call(n.exports,n,n.exports,e),n.l=!0,n.exports}var a={};e.m=t,e.c=a,e.i=function(t){return t},e.d=function(t,a,o){e.o(t,a)||Object.defineProperty(t,a,{configurable:!1,enumerable:!0,get:o})},e.n=function(t){var a=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(a,"a",a),a},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=366)}({153:function(t,e){!function(t){function e(){return{item:[],errors:[],product_sale:null,product_weight:null,product_avalability:null}}var a=new Vue({el:"#data_scrapping",data:{items:[]},methods:{setTokopediaDownloadItem:function(e){o.item=e,t.ajax({url:t("#data-product").attr("data-url-scrapping-tokopedia-product-detail"),method:"POST",headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")},data:{merchant:e.store,slug:e.slug}}).done(function(t){o.$set(o.item,"images",t[0].data.getPDPInfo.pictures),o.$set(o.item,"description",t[0].data.getPDPInfo.basic.description),o.$set(o.item,"product_weight",t[0].data.getPDPInfo.basic.weight/1e3),t[0].data.getPDPInfo.campaign.discountedPrice>0?o.$set(o.item,"price",t[0].data.getPDPInfo.campaign.discountedPrice):o.$set(o.item,"price",t[0].data.getPDPInfo.basic.price),o.$set(o.item,"is_variant",t[0].data.getPDPInfo.variant.isVariant),o.$set(o.item,"condition",t[0].data.getPDPInfo.basic.condition.toLowerCase())}).fail(function(){}),t("#modal-tokopedia-download").modal("show")}}});t(document).ready(function(){t("#submit-data-scrapping").click(function(e){var o={url:t("#data-web-scrapping").attr("action"),method:"POST",headers:{accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")},data:t("#data-web-scrapping").serialize()};t.ajax(o).done(function(t){a.items=t.list}).fail(function(t){alert(JSON.stringify(t.responseJSON.errors))})})});var o=new Vue({el:"#tokopedia_download",data:function(){return e()},methods:{resetWindow:function(){Object.assign(this.$data,e())},removeImage:function(t){this.item.images.splice(t,1)},addImage:function(){this.item.images.push({urlOriginal:""})},submit:function(){self=this,t.ajax({url:t("#tokopedia_download").attr("data-action"),type:"POST",data:t("#tokopedia_download").serialize(),headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")}}).done(function(){self.errors=[],t("#reload-datatable").click(),t("#modal-tokopedia-download").modal("hide"),t("#submit-data-scrapping").click()}).fail(function(t){self.errors=t.responseJSON.errors})}}});window.TokopediaDownload=o,t(document).ready(function(){t("#modal-tokopedia-download").on("hidden.bs.modal",function(t){o.resetWindow()})})}(jQuery)},366:function(t,e,a){t.exports=a(153)}});