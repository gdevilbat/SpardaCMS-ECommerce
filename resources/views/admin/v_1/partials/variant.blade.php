<div class="tab-pane" id="variant" role="tabpanel">
    <div class="form-group m-form__group d-flex">
        <div class="col">
            <div class="mb-5" v-for="(variant, index_variant) in (variants)">
                <div class="d-flex my-1">
                    <div class="col-md-10">
                      <div class="col-12 d-flex">
                        <div class="col-2 d-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Name<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control m-input" placeholder="Nama Variasi" v-bind:name="'meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT}}][variants]['+index_variant+'][name]'" v-model="variant.name">
                        </div>
                      </div>
                      <div class="col-12 d-flex">
                        <div class="col-2 d-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Option<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                        </div>
                        <div class="col d-flex flex-wrap">
                            <div class="col-12 d-flex mb-2" v-for="(option, index_option) in (variant.option)">
                              <input type="text" class="form-control m-input" placeholder="Nama Option" v-bind:name="'meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT}}][variants]['+index_variant+'][option]['+index_option+'][value]'" v-model="option.value">
                              <div class="col" v-if="variant.option.length > 1">
                                  <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeOption(variant, index_option)"><span><i class="fa fa-minus"></i></span></button>
                              </div>
                            </div>
                            <div class="col-12">
                              <button type="button" class="btn btn-success" v-on:click="addOption(variant)">Add Option</button>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                        <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeVariants(index_variant)"><span><i class="fa fa-minus"></i></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group m-form__group d-flex">
        <div class="col-md-6 offset-md-4">
            <button type="button" class="btn btn-success" v-on:click="addVariants">Add Varian</button>
        </div>
    </div>
    <div class="form-group m-form__group d-flex" v-if="children.length > 0">
      <div class="col-2 d-flex justify-content-end py-3">
          <label for="exampleInputEmail1">Daftar Harga</label>
      </div>
      <div class="col">
        <table class="table table-bordered display responsive nowrap dataTable no-footer dtr-inline collapsed">
          <thead>
            <tr>
              <th>Varian</th>
              <th width="200px">Harga</th>
              <th width="200px">Stok</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(child, index) in (children)">
              <td>
                @{{ child.name }}
                <input class="form-control w-100" type="hidden" v-bind:name="'meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT}}][children]['+index+'][name]'" v-model="child.name">
              </td>
              <td>
                <input class="form-control w-100" type="number" v-bind:name="'meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT}}][children]['+index+'][price]'" v-model="child.price" min="0">
              </td>
              <td>
                <input class="form-control w-100" type="number" v-bind:name="'meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT}}][children]['+index+'][stock][stock]'" v-model="child.stock.stock" v-model="child.stock" min="0">
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</div>

@push('page_script_js')
  <script type="text/javascript">
    var Variant = new Vue({
        el: "#variant",
        data: {
            variants: {!! old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT.'.variants') ? json_encode(old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT.'.variants')) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT)->variants) ? json_encode($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT)->variants) : json_encode([])) !!},
            children: {!! old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT.'.children') ? json_encode(old('meta.'.Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT.'.children')) : (!empty($post) && !empty($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT)->children) ? json_encode($post->meta->getMetaData(Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_VARIANT)->children) : json_encode([])) !!},
        },
        methods: {
          addOption: function(variant){
           variant.option.push({'value': ''});
          },
          removeOption: function(variant, index){
           variant.option.splice(index, 1);
          },
          addVariants: function(){
              this.variants.push({name : '', option : [{'value' : ''}]});
          },
          removeVariants: function(index){
              this.variants.splice(index, 1);
          },
          cartesianProduct: function(...arr){
            return arr.reduce((acc,val) => {
                return acc.map(el => {
                   return val.map(element => {
                      return el.concat([element]);
                   });
                }).reduce((acc,val) => acc.concat(val) ,[]);
             }, [[]]);
          }
        },
        watch: {
          variants: {
            handler(val){
              self = this;
             let args = [];
             $.each(this.variants, function(index_variant, variant) {
                let index_key = [];
                $.each(variant.option, function(index_option, option) {
                  index_key.push(index_option);
                });
                args.push(index_key);
             });

             let data = this.cartesianProduct.apply(this, args);
             $.each(data, function(index, val) {
                let name  = '';
                $.each(val, function(index, value) {
                    if(index > 0){
                      name += ', ';
                    }
                      name += self.variants[index].option[value].value;

                });

                if(self.children[index] == undefined){
                  self.$set(self.children, index, {'name' : name, 'price': '', 'stock': {stock: ''}});
                }else{
                  let old_data = self.children[index];
                  old_data.name = name;
                  self.$set(self.children, index, old_data);
                }
             });

             sub = self.children.length - data.length;

             if(sub > 0){
              self.children.splice(-1, sub);
             }

             if(data[0].length == 0){
              self.children.splice(0, self.children.length);
             }
           },
           deep: true
          }
        }
    });
  </script>
@endpush