!function(t){function a(o){if(e[o])return e[o].exports;var n=e[o]={i:o,l:!1,exports:{}};return t[o].call(n.exports,n,n.exports,a),n.l=!0,n.exports}var e={};a.m=t,a.c=e,a.i=function(t){return t},a.d=function(t,e,o){a.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:o})},a.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return a.d(e,"a",e),e},a.o=function(t,a){return Object.prototype.hasOwnProperty.call(t,a)},a.p="",a(a.s=366)}({153:function(t,a){!function(t){function a(){return{item:[],errors:[],product_sale:null,product_weight:null,product_avalability:null}}var e=new Vue({el:"#data_scrapping",data:{items:[]},methods:{setTokopediaDownloadItem:function(a){o.item=a,t.ajax({url:t("#data-product").attr("data-url-scrapping-tokopedia-product-detail"),method:"POST",headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")},data:{merchant:a.store,slug:a.slug}}).done(function(a){o.$set(o.item,"product_id",a[0].data.pdpGetLayout.basicInfo.id);var e=a[0].data.pdpGetLayout.components[0].data[0].media;t.each(e,function(t,a){e[t].urlOriginal=a.prefix+"700"+a.suffix}),o.$set(o.item,"images",e),o.$set(o.item,"description",a[0].data.pdpGetLayout.components[4].data[0].content[5].subtitle),o.$set(o.item,"product_weight",a[0].data.pdpGetLayout.basicInfo.weight/1e3),a[0].data.pdpGetLayout.components[3].data[0].campaign.discountedPrice>0?o.$set(o.item,"price",a[0].data.pdpGetLayout.components[3].data[0].campaign.discountedPrice):o.$set(o.item,"price",a[0].data.pdpGetLayout.components[3].data[0].price.value),o.$set(o.item,"is_variant",a[0].data.pdpGetLayout.components[3].data[0].variant.isVariant),o.$set(o.item,"condition",a[0].data.pdpGetLayout.basicInfo.condition.toLowerCase()),a[0].data.pdpGetLayout.components[3].data[0].variant.isVariant&&o.$set(o.item,"children",a[0].data.pdpGetLayout.components[2].data[0].children)}).fail(function(){}),t("#modal-tokopedia-download").modal("show")}}});t(document).ready(function(){t("#submit-data-scrapping").click(function(a){var o={url:t("#data-web-scrapping").attr("action"),method:"POST",headers:{accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")},data:t("#data-web-scrapping").serialize()};t.ajax(o).done(function(t){e.items=t.list}).fail(function(t){alert(JSON.stringify(t.responseJSON.errors))})})});var o=new Vue({el:"#tokopedia_download",data:function(){return a()},methods:{resetWindow:function(){Object.assign(this.$data,a())},removeChildren:function(t){this.item.children.splice(t,1)},removeImage:function(t){this.item.images.splice(t,1)},addImage:function(){this.item.images.push({urlOriginal:""})},addChildren:function(){this.item.children.push({product_id:""})},submit:function(){self=this,t.ajax({url:t("#tokopedia_download").attr("data-action"),type:"POST",data:t("#tokopedia_download").serialize(),headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")}}).done(function(){self.errors=[],t("#reload-datatable").click(),t("#modal-tokopedia-download").modal("hide"),t("#submit-data-scrapping").click()}).fail(function(t){self.errors=t.responseJSON.errors})}}});window.TokopediaDownload=o,t(document).ready(function(){t("#modal-tokopedia-download").on("hidden.bs.modal",function(t){o.resetWindow()})})}(jQuery)},366:function(t,a,e){t.exports=e(153)}});