!function(t){var e={};function o(n){if(e[n])return e[n].exports;var a=e[n]={i:n,l:!1,exports:{}};return t[n].call(a.exports,a,a.exports,o),a.l=!0,a.exports}o.m=t,o.c=e,o.d=function(t,e,n){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var a in t)o.d(n,a,function(e){return t[e]}.bind(null,a));return n},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="/",o(o.s=362)}({362:function(t,e,o){t.exports=o(363)},363:function(t,e){$(document).ready((function(){if($("#boosted-item").length>0)new Vue({el:"#boosted-item",data:{items:[]},mounted:function(){this.$nextTick((function(){var t=this;$.ajax({url:$("#boosted-item").attr("data-url"),type:"POST",data:{shop_id:$("[name='shop_id']").val()},headers:{Accept:"application/json",Authorization:"Bearer "+$("meta[name='api-token']").attr("content")}}).done((function(e){var o=e.items;$.each(o,(function(e,o){$.ajax({url:$("#boosted-item").attr("data-url-item"),type:"POST",data:{shop_id:$("[name='shop_id']").val(),product_id:o.item_id},headers:{Accept:"application/json",Authorization:"Bearer "+$("meta[name='api-token']").attr("content")}}).done((function(e){t.items.push(e)}))}))}))}))}});if($("#shopee-info").length>0)new Vue({el:"#shopee-info",data:{store:[]},mounted:function(){this.$nextTick((function(){var t=this;$.ajax({url:$("#shopee-info").attr("data-url"),type:"POST",data:{shop_id:$("[name='shop_id']").val()},headers:{Accept:"application/json",Authorization:"Bearer "+$("meta[name='api-token']").attr("content")}}).done((function(e){t.store=e}))}))}})})),function(t){var e=new Vue({el:"#shopee_upload",data:function(){return{id_posts:null,item:[],category_id:null,categories:[],logistics:[],attributes:[],selected_category:[],children_categories:[],selected_logistic:[],errors:[],attribute_option_index:null,is_pre_order:!1}},methods:{addSelected:function(e,o){var n=this,a=this.categories;this.children_categories=[],this.attributes=[],this.category_id=null,this.$set(this.selected_category,o,e.target.value),this.$nextTick((function(){t.each(this.selected_category,(function(t,e){o<=t&&(n.selected_category.splice(t+1,1),document.getElementById("children_"+(t+1)).value="")}))})),t.each(n.selected_category,(function(e,o){a[o].has_children?(n.$set(n.children_categories,e,a[o].children),a=a[o].children):(n.selected_category.splice(e,1),n.category_id=a[o].category_id,t.ajax({url:t("#shopee_form").attr("data-url-shopee-attribute"),data:{category_id:n.category_id,shop_id:t("[name='shop_id']").val()}}).done((function(t){n.attributes=t.attributes})).fail((function(){console.log("error")})))}))},setAttributeIndex:function(t){this.attribute_option_index=t},addAttributeOption:function(e){last=this.attributes[e].options.length,this.$set(this.attributes[e].options,last,t("#attribute_option_"+e).val()),this.attribute_option_index=null},setDataForm:function(e){this.id_posts=e;var o=this;t.ajax({url:t("#shopee_form").attr("data-url-product-detail"),type:"POST",data:{id_posts:this.id_posts},headers:{Accept:"application/json"}}).done((function(e){o.item=e,t("#modal-shopee-upload").modal("show"),t.ajax({url:t("#shopee_form").attr("data-url-shopee-logistics"),data:{shop_id:t("[name='shop_id']").val()}}).done((function(t){o.logistics=t})).fail((function(){console.log("error")})),window.objSize(e.product_variant)>0?(Variant_shopee_upload.variants=e.product_variant.variants,Variant_shopee_upload.children=e.product_variant.children):(Variant_shopee_upload.variants=[],Variant_shopee_upload.children=[])})).fail((function(){console.log("error")}))},resetWindow:function(){Object.assign(this.$data,{id_posts:null,item:[],category_id:null,categories:[],logistics:[],attributes:[],selected_category:[],children_categories:[],selected_logistic:[],errors:[],attribute_option_index:null,is_pre_order:!1})},submit:function(e){e.preventDefault();var o=this;t.ajax({url:t("#shopee_form").attr("data-action"),type:"POST",data:t("#shopee_form").serialize(),headers:{Accept:"application/json",Authorization:"Bearer "+t("meta[name='api-token']").attr("content")}}).done((function(){o.errors=[],t("#reload-datatable").click(),t("#modal-shopee-upload").modal("hide")})).fail((function(t){o.errors=t.responseJSON.errors}))}}});window.ShopeeUpload=e,t(document).ready((function(){t("#modal-shopee-upload").on("hidden.bs.modal",(function(t){e.resetWindow(),Variant_shopee_upload.resetWindow()})),t("#modal-shopee-upload").on("shown.bs.modal",(function(){t.ajax({url:t("#shopee_form").attr("data-url-shopee-category"),data:{shop_id:t("[name='shop_id']").val()}}).done((function(t){e.categories=t})).fail((function(){console.log("error")}))}))}))}(jQuery),window.popupWindow=function(t,e,o,n,a){var i=o.top.outerHeight/2+o.top.screenY-a/2,r=o.top.outerWidth/2+o.top.screenX-n/2;return o.open(t,e,"toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=".concat(n,", height=").concat(a,", top=").concat(i,", left=").concat(r))}}});