<div class="tab-pane" id="marketplace" role="tabpanel" data-url-scrapping-tokopedia-product-detail="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ScrappingController@scrappingTokopediaProductDetail')}}" data-url-scrapping-shopee="{{action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ScrappingController@scrappingShopee')}}" v-cloak>
  <div class="form-group m-form__group d-flex px-0">
      <div class="col-4 d-flex justify-content-end py-3">
          <label for="exampleInputEmail1">{{ ucwords(str_replace('_', ' ', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER)) }}</label>
      </div>
      <div class="col">
          <input type="text" class="form-control m-input slug-me" placeholder="Tokopedia Merchant" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][merchant]" v-model="tokopedia_supplier.merchant" v-on:change="resetTokopediaData('tokopedia_supplier')">
          <input type="text" class="form-control m-input slug-me" placeholder="Tokopedia Slug" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][slug]" v-model="tokopedia_supplier.slug" v-on:change="resetTokopediaData('tokopedia_supplier')">
          <div class="col-12 d-flex mx-0 px-0" v-if="tokopedia_supplier.merchant != '' && tokopedia_supplier.slug != ''">
              <div class="col pl-0">
                  <input type="text" class="form-control m-input" placeholder="Tokopedia Product ID" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][product_id]" v-model="tokopedia_supplier.product_id" readonly>
              </div>
              <button type="button" class="btn btn-success" v-on:click="getTokopediaData('tokopedia_supplier')">Get Data</button>
          </div>
          <input type="hidden" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][is_variant]" v-bind:value="tokopedia_supplier.is_variant">
          <div class="form-group m-form__group row">
              <label for="example-text-input" class="col-3 col-form-label">Is Variant</label>
              <div class="col">
                  <div class="col-12">
                      <span class="m-switch m-switch--icon m-switch--success ml-1 d-flex align-items-center">
                          <label>
                              <label>
                                  <input type="checkbox" v-model="tokopedia_supplier.is_variant" value="true">
                                  <span></span>
                              </label>
                          </label>
                      </span>
                  </div>
              </div>
          </div>
          <div v-if="tokopedia_supplier.is_variant">
              <div class="col-12 d-flex" v-for="(children, index) in tokopedia_supplier.children">
                  <div class="col">
                      <input  class="form-control" type="text" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][children][][product_id]" v-model="children.product_id" placeholder="Product ID">
                  </div>
                  <div class="col-2">
                      <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeChildren('tokopedia_supplier',index)"><span><i class="fa fa-minus"></i></span></button>
                  </div>
              </div>
              <div class="col-md-6 mt-2">
                  <button type="button" class="btn btn-success" v-on:click="addChildren('tokopedia_supplier')">Add Children</button>
              </div>
          </div>
      </div>
  </div>
  <div class="form-group m-form__group d-flex px-0">
      <div class="col-4 d-flex justify-content-end py-3">
          <label for="exampleInputEmail1">{{ ucwords(str_replace('_', ' ', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER)) }}</label>
      </div>
      <div class="col">
          <input type="text" class="form-control m-input" placeholder="Shop ID" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER}}][shop_id]" v-model="shopee_supplier.shop_id" v-on:change="resetShopeeData('shopee_supplier')">
          <div class="col-12 d-flex px-0">
              <div class="col pl-0">
                  <input type="text" class="form-control m-input" placeholder="Product ID" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER}}][product_id]" v-model="shopee_supplier.product_id" v-on:change="resetShopeeData('shopee_supplier')">
              </div>
              <button type="button" class="btn btn-success" v-on:click="getShopeeData('shopee_supplier')">Get Data</button>
          </div>
          <input type="hidden" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER}}][is_variant]" v-bind:value="shopee_supplier.is_variant">
          <div class="form-group m-form__group row">
              <label for="example-text-input" class="col-3 col-form-label">Is Variant</label>
              <div class="col">
                  <div class="col-12">
                      <span class="m-switch m-switch--icon m-switch--success ml-1 d-flex align-items-center">
                          <label>
                              <label>
                                  <input type="checkbox" v-model="shopee_supplier.is_variant" value="true">
                                  <span></span>
                              </label>
                          </label>
                      </span>
                  </div>
              </div>
          </div>
          <div v-if="shopee_supplier.is_variant">
              <div class="col-12 d-flex" v-for="(children, index) in shopee_supplier.children">
                  <div class="col">
                      <input  class="form-control" type="text" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER}}][children][][product_id]" v-model="children.product_id" placeholder="Product ID">
                  </div>
                  <div class="col-2">
                      <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeChildren('shopee_supplier',index)"><span><i class="fa fa-minus"></i></span></button>
                  </div>
              </div>
              <div class="col-md-6 mt-2">
                  <button type="button" class="btn btn-success" v-on:click="addChildren('shopee_supplier')">Add Children</button>
              </div>
          </div>
      </div>
  </div>
  <hr>
  <div class="form-group m-form__group d-flex px-0">
      <div class="col-4 d-flex justify-content-end py-3">
          <label for="exampleInputEmail1">{{ ucwords(str_replace('_', ' ', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE)) }}</label>
      </div>
      <div class="col">
          <input type="text" class="form-control m-input slug-me" placeholder="Tokopedia Merchant" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE}}][merchant]" v-model="tokopedia_store.merchant" v-on:change="resetTokopediaData('tokopedia_store')">
          <input type="text" class="form-control m-input slug-me" placeholder="Tokopedia Slug" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE}}][slug]" v-model="tokopedia_store.slug" v-on:change="resetTokopediaData('tokopedia_store')">
          <div class="col-12 d-flex mx-0 px-0" v-if="tokopedia_store.merchant != '' && tokopedia_store.slug != ''">
              <div class="col pl-0">
                  <input type="text" class="form-control m-input" placeholder="Tokopedia Product ID" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE}}][product_id]" v-model="tokopedia_store.product_id" readonly>
              </div>
              <button type="button" class="btn btn-success" v-on:click="getTokopediaData('tokopedia_store')">Get Data</button>
          </div>
          <input type="hidden" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE}}][is_variant]" v-bind:value="tokopedia_store.is_variant">
          <div class="form-group m-form__group row">
              <label for="example-text-input" class="col-3 col-form-label">Is Variant</label>
              <div class="col">
                  <div class="col-12">
                      <span class="m-switch m-switch--icon m-switch--success ml-1 d-flex align-items-center">
                          <label>
                              <label>
                                  <input type="checkbox" v-model="tokopedia_store.is_variant" value="true">
                                  <span></span>
                              </label>
                          </label>
                      </span>
                  </div>
              </div>
          </div>
          <div v-if="tokopedia_store.is_variant">
              <div class="col-12 d-flex" v-for="(children, index) in tokopedia_store.children">
                  <div class="col">
                      <input  class="form-control" type="text" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE}}][children][][product_id]" v-model="children.product_id" placeholder="Product ID">
                  </div>
                  <div class="col-2">
                      <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeChildren('tokopedia_store',index)"><span><i class="fa fa-minus"></i></span></button>
                  </div>
              </div>
              <div class="col-md-6 mt-2">
                  <button type="button" class="btn btn-success" v-on:click="addChildren('tokopedia_store')">Add Children</button>
              </div>
          </div>
      </div>
  </div>
  <div class="form-group m-form__group d-flex px-0">
      <div class="col-4 d-flex justify-content-end py-3">
          <label for="exampleInputEmail1">{{ ucwords(str_replace('_', ' ', \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE)) }}</label>
      </div>
      <div class="col">
          <input type="text" class="form-control m-input" placeholder="Shop ID" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE}}][shop_id]" v-model="shopee_store.shop_id" v-on:change="resetShopeeData('shopee_store')">
          <div class="col-12 d-flex px-0">
              <div class="col pl-0">
                  <input type="text" class="form-control m-input" placeholder="Product ID" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE}}][product_id]" v-model="shopee_store.product_id" v-on:change="resetShopeeData('shopee_store')">
              </div>
              <button type="button" class="btn btn-success" v-on:click="getShopeeData('shopee_store')">Get Data</button>
          </div>
          <input type="hidden" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE}}][is_variant]" v-bind:value="shopee_store.is_variant">
          <div class="form-group m-form__group row">
              <label for="example-text-input" class="col-3 col-form-label">Is Variant</label>
              <div class="col">
                  <div class="col-12">
                      <span class="m-switch m-switch--icon m-switch--success ml-1 d-flex align-items-center">
                          <label>
                              <label>
                                  <input type="checkbox" v-model="shopee_store.is_variant" value="true">
                                  <span></span>
                              </label>
                          </label>
                      </span>
                  </div>
              </div>
          </div>
          <div v-if="shopee_store.is_variant">
              <div class="col-12 d-flex" v-for="(children, index) in shopee_store.children">
                  <div class="col">
                      <input  class="form-control" type="text" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE}}][children][][product_id]" v-model="children.product_id" placeholder="Product ID">
                  </div>
                  <div class="col-2">
                      <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeChildren('shopee_store',index)"><span><i class="fa fa-minus"></i></span></button>
                  </div>
              </div>
              <div class="col-md-6 mt-2">
                  <button type="button" class="btn btn-success" v-on:click="addChildren('shopee_store')">Add Children</button>
              </div>
          </div>
      </div>
  </div>
</div>

@push('page_script_js')
  <script type="text/javascript">
    var Marketplace = new Vue({
            el: "#marketplace",
            data:{
                'tokopedia_supplier' : {!! old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER) ? json_encode(old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER)) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER)) ? json_encode($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER)->get()) : json_encode(['children' => [], 'merchant' => '', 'slug' => '', 'is_variant' => 'false'])) !!},
                'shopee_supplier' : {!! old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER) ? json_encode(old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER)) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER)) ? json_encode($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_SUPPLIER)->get()) : json_encode(['children' => [], 'shop_id' => '', 'product_id' => '', 'is_variant' => 'false'])) !!},
                'tokopedia_store' : {!! old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE) ? json_encode(old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE)) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE)) ? json_encode($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_STORE)->get()) : json_encode(['children' => [], 'merchant' => '', 'slug' => '', 'is_variant' => 'false'])) !!},
                'shopee_store' : {!! old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE) ? json_encode(old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE)) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE)) ? json_encode($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_STORE)->get()) : json_encode(['children' => [], 'shop_id' => '', 'product_id' => '', 'is_variant' => 'false'])) !!},
            },
            methods: {
                removeChildren: function($attr, index){
                    this[$attr].children.splice(index, 1);
                },
                addChildren: function($attr){
                    if(this[$attr].children == undefined)
                        this.$set( this[$attr], 'children', []);

                    this[$attr].children.push({product_id: ''});
                },
                resetTokopediaData: function($attr){
                    this.$set( this[$attr], 'product_id', '');
                    this.$set( this[$attr], 'is_variant', false);
                },
                resetShopeeData: function($attr){
                    this.$set( this[$attr], 'is_variant', false);
                },
                getTokopediaData: function($attr){
                    self = this;
                    $.ajax({
                      url: $('#marketplace').attr('data-url-scrapping-tokopedia-product-detail'),
                      method: "POST",
                      headers: {
                        "Accept": "application/json",
                            "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                      },
                      data: {merchant: self[$attr].merchant, slug: self[$attr].slug}
                    }).done(function(response){
                        if(response.errors == null)
                        {
                            self.$set( self[$attr], 'product_id', response.data.pdpGetLayout.basicInfo.id);
                            self.$set( self[$attr], 'is_variant', response.data.pdpGetLayout.components[3].data[0].variant.isVariant);


                            if(self[$attr].is_variant)
                            {
                                children = response.data.pdpGetLayout.components[2].data[0].children;
                                
                                $.each(children, function(index, val) {
                                     children[index]['product_id'] = val.productID;
                                });

                                self.$set( self[$attr], 'children', children);
                            }

                        }
                        else
                        {
                            alert('Product Tidak Ditemukan, Periksa Merchant dan Slug');
                        }
                    }).fail(function() {
                      console.log("error");
                    })
                },
                getShopeeData: function($attr){
                    self = this;
                    $.ajax({
                      url: $('#marketplace').attr('data-url-scrapping-shopee'),
                      method: "POST",
                      dataType: "json",
                      headers: {
                        "Accept": "application/json",
                            "Authorization": "Bearer "+ $("meta[name='api-token']").attr('content')
                      },
                      data: {shopid: self[$attr].shop_id, itemid: self[$attr].product_id}
                    }).done(function(response){
                        if(response.item != null)
                        {
                            self.$set( self[$attr], 'is_variant', true);

                            children = response.item.models;

                            if(self[$attr].is_variant)
                            {
                                $.each(children, function(index, val) {
                                     children[index]['product_id'] = val.modelid;
                                });

                                self.$set( self[$attr], 'children', children);
                            }

                        }
                        else
                        {
                            alert('Product Tidak Ditemukan, Periksa Shop ID dan Product ID');
                        }
                    }).fail(function() {
                      console.log("error");
                    })
                }
            },
            mounted: function(){
                this.$nextTick(function(){
                    this.tokopedia_supplier.is_variant = JSON.parse(this.tokopedia_supplier.is_variant)
                    this.shopee_supplier.is_variant = JSON.parse(this.shopee_supplier.is_variant)
                    this.tokopedia_store.is_variant = JSON.parse(this.tokopedia_store.is_variant)
                    this.shopee_store.is_variant = JSON.parse(this.shopee_store.is_variant)
                });
            }
        });
  </script>
@endpush