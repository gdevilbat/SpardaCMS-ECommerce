!function(t){var e={};function a(o){if(e[o])return e[o].exports;var n=e[o]={i:o,l:!1,exports:{}};return t[o].call(n.exports,n,n.exports,a),n.l=!0,n.exports}a.m=t,a.c=e,a.d=function(t,e,o){a.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:o})},a.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},a.t=function(t,e){if(1&e&&(t=a(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(a.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)a.d(o,n,function(e){return t[e]}.bind(null,n));return o},a.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return a.d(e,"a",e),e},a.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},a.p="/",a(a.s=123)}({123:function(t,e,a){t.exports=a(124)},124:function(t,e){!function(t){var e=new Vue({el:"#data_scrapping",data:{items:[]},methods:{setTokopediaDownloadItem:function(e){a.item=e,t.ajax({url:t("#data_product").attr("data-url-scrapping-tokopedia-product-detail"),method:"POST",headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")},data:{merchant:e.store,slug:e.slug}}).done((function(e){a.$set(a.item,"product_id",e.data.pdpGetLayout.basicInfo.id);var o=e.data.pdpGetLayout.components[0].data[0].media;t.each(o,(function(t,e){o[t].urlOriginal=e.prefix+"700"+e.suffix})),a.$set(a.item,"images",o),t.each(e.data.pdpGetLayout.components[4].data[0].content,(function(t,e){"Deskripsi"==e.title&&a.$set(a.item,"description",e.subtitle)})),a.$set(a.item,"product_weight",e.data.pdpGetLayout.basicInfo.weight/1e3),e.data.pdpGetLayout.components[3].data[0].campaign.discountedPrice>0?a.$set(a.item,"price",e.data.pdpGetLayout.components[3].data[0].campaign.discountedPrice):a.$set(a.item,"price",e.data.pdpGetLayout.components[3].data[0].price.value),a.$set(a.item,"is_variant",e.data.pdpGetLayout.components[3].data[0].variant.isVariant),a.$set(a.item,"condition",e.data.pdpGetLayout.basicInfo.condition.toLowerCase()),e.data.pdpGetLayout.components[3].data[0].variant.isVariant&&(a.$set(a.item,"children",e.data.pdpGetLayout.components[2].data[0].children),a.$set(Variant_tokopedia_download,"children",e.data.pdpGetLayout.components[2].data[0].sorted_children_by_option_id),a.$set(Variant_tokopedia_download,"variants",e.data.pdpGetLayout.components[2].data[0].variants))})).fail((function(){console.log("error")})),t("#modal-tokopedia-download").modal("show")}}});t(document).ready((function(){t("#submit-data-scrapping").click((function(a){var o={url:t("#data-web-scrapping").attr("action"),method:"POST",headers:{accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")},data:t("#data-web-scrapping").serialize()};t.ajax(o).done((function(t){e.items=t.list})).fail((function(t){alert(JSON.stringify(t.responseJSON.errors))}))}))}));var a=new Vue({el:"#tokopedia_download",data:function(){return{item:[],errors:[],product_sale:null,product_weight:null,product_avalability:null,product_stock:null}},methods:{resetWindow:function(){Object.assign(this.$data,{item:[],errors:[],product_sale:null,product_weight:null,product_avalability:null,product_stock:null})},removeChildren:function(t){this.item.children.splice(t,1)},removeImage:function(t){this.item.images.splice(t,1)},addImage:function(){this.item.images.push({urlOriginal:""})},addChildren:function(){this.item.children.push({product_id:""})},submit:function(e){e.preventDefault(),self=this,t.ajax({url:t("#tokopedia_form").attr("data-action"),type:"POST",data:t("#tokopedia_form").serialize(),headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")}}).done((function(){self.errors=[],t("#reload-datatable").click(),t("#modal-tokopedia-download").modal("hide"),t("#submit-data-scrapping").click()})).fail((function(t){self.errors=t.responseJSON.errors}))}}});window.TokopediaDownload=a,t(document).ready((function(){t("#modal-tokopedia-download").on("hidden.bs.modal",(function(t){a.resetWindow(),Variant_tokopedia_download.resetWindow()}))}))}(jQuery)}});