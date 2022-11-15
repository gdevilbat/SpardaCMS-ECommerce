<div class="modal fade" id="modal-lazada-upload" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel" data-dismiss="modal">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <form data-action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\LazadaController@itemAdd') }}" data-url-lazada-category="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\LazadaController@itemGetCategories') }}"  data-url-lazada-attribute="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\LazadaController@itemGetAttributes') }}" data-url-lazada-logistics="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\LazadaController@getLogistics') }}" data-url-product-detail="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@apiProductDetail') }}" id="lazada_form" onsubmit="LazadaUpload.submit(event)">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Lazada Upload</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                    <div class="row" id="lazada_upload" v-cloak>
                    	<div class="col-12" v-if="objSize(errors) > 0">
                    		<div class="alert alert-danger">
	                            <ul v-for="error in errors">
                                    <li v-for="message in error">@{{ message }}</li>
	                            </ul>
	                        </div>
                    	</div>
                        <div class="col-12">
                            <div>
                                <div class="form-group m-form__group row">
                                    <label for="example-text-input" class="col-3 col-form-label">Product Name <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    <div class="col">
                                        <input class="form-control m-input" type="text" name="product_name" v-model="item.post_title">
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label for="example-text-input" class="col-3 col-form-label">Product Image <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    <div class="col">
                                        <div v-for="(image, index) in item.image_url">
                                            <input class="form-control m-input my-1" type="text" name="product_image[]" v-model="item.image_url[index]">
                                            <img v-bind:src="item.image_url[index]" width="200" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Category <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col pl-0">
                                    <div class="col-12">
                                        <select class="form-control" v-on:change="addSelected($event, 0)" required>
                                            <option value="" selected disabled>Select Category</option>
                                            <option v-for="(category, index) in categories" v-bind:value="index">@{{ category.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-2" v-for="(selected, index) in selected_category">
                                        <select v-bind:id="'children_'+(index+1)" class="form-control" v-on:change="addSelected($event, (index+1))" required>
                                            <option value="" selected disabled>Select Category</option>
                                            <option v-bind:value="index_children" v-for="(category, index_children) in children_categories[index]"> @{{ category.name }} </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row" v-if="attributes.length > 0">
                                <label for="example-text-input" class="col-3 col-form-label">Product Attributes</label>
                                <div class="col pl-0">
                                    <div class="col-12 mt-2" v-for="(attribute, index) in attributes">
                                        <div class="row">
                                            <div class="col-1 pr-0">
                                                <span class="ml-1 m--font-danger" aria-required="true" v-if="attribute.is_mandatory">*</span>
                                            </div>
                                            <div class="col pl-0">
                                                <input type="hidden" v-bind:name="'product_attributes['+index+'][attribute_type]'" v-bind:value="attribute.attribute_type">
                                                <input type="hidden" v-bind:name="'product_attributes['+index+'][attribute_name]'" v-bind:value="attribute.name">
                                                <input v-if="attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::LAZADA_ATTR_TEXT ?>'" class="form-control" type="text" v-bind:name="'product_attributes['+index+'][value]'" v-bind:placeholder="attribute.label" v-bind:required="attribute.is_mandatory ? true : false" v-bind:value="getModelValue(index, attribute.name)" v-bind:readonly="isReadOnly(attribute.name)">
                                                <div class="row" v-if="attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::LAZADA_ATTR_COMBO ?>' || attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::LAZADA_ATTR_DROPDOWN ?>'">
                                                    <div class="col">
                                                        <select class="form-control" v-bind:name="'product_attributes['+index+'][value]'" v-bind:placeholder="attribute.label" v-bind:required="attribute.is_mandatory ? true : false">
                                                            <option value="">--Select @{{ attribute.label }}--</option>
                                                            <option v-for="option in attribute.options">@{{ option.name }}</option>
                                                        </select>
                                                        <div v-if="attribute_option_index == index">
                                                            <input v-bind:id="'attribute_option_'+index" type="text"> <button type="button" v-on:click="addAttributeOption(index)">Tambah</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn m-btn--pill btn-danger" v-if="attribute.attribute_type == 'normal'" v-on:click="setAttributeIndex(index)"><span><i class="fa fa-plus"></i></span></button>
                                                    </div>
                                                </div>
                                                <textarea class="form-control" v-if="attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::LAZADA_ATTR_LONGTEXT ?>'" v-bind:name="'product_attributes['+index+'][value]'" v-bind:placeholder="attribute.label" v-bind:required="attribute.is_mandatory ? true : false" v-bind:value="getModelValue(index, attribute.name)" v-bind:readonly="isReadOnly(attribute.name)"></textarea>
                                                <input type="number" class="form-control" v-if="attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::LAZADA_ATTR_NUMERIC ?>'" v-bind:name="'product_attributes['+index+'][value]'" v-bind:placeholder="attribute.label" v-bind:required="attribute.is_mandatory ? true : false" v-bind:value="getModelValue(index, attribute.name)" v-bind:readonly="isReadOnly(attribute.name)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Price <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <input class="form-control m-input" type="number" name="product_price" v-model="item.price">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Stock <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <input class="form-control m-input" type="number" name="product_stock" v-model="item.product_stock" min="0">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Weight <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <input class="form-control m-input" type="number" name="product_weight" step="0.01" min="0.01" v-model="item.product_weight">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Description <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <textarea name="product_description" class="form-control autosize" v-model="item.post_content"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="category_id" v-model="category_id" required>
                        <input type="hidden" name="id_posts" v-model="id_posts" required>
                    </div>
                    @include('ecommerce::admin.v_1.partials.variant', ['id' => 'lazada_upload'])
              </div>
              {{ csrf_field() }}
              <input type="hidden" class="form-control m-input" name="shop_id" value="{{getSettingConfig('lazada_id')}}">
              <input type="hidden" class="form-control m-input" name="access_token" value="{{MarketPlace::driver('lazada')->shop->getAccessToken(getSettingConfig('lazada_id'))}}">
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Upload</button>
                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
              </div>
          </form>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

@push('page_script_js')
    {{Html::script(module_asset_url('Ecommerce:resources/views/admin/v_1/js/lazada.js').'?id='.filemtime(module_asset_path('ecommerce:resources/views/admin/v_1/js/lazada.js')))}}
@endpush