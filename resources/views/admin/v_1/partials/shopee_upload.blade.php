<div class="modal fade" id="modal-shopee-upload" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel" data-dismiss="modal">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <form data-action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Micro\ShopeeController@itemAdd') }}" cloak data-url-shopee-category="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getCategories') }}"  data-url-shopee-attribute="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getAttributes') }}" data-url-shopee-logistics="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@getLogistics') }}" data-url-product-detail="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ProductController@apiProductDetail') }}" id="shopee_upload" v-on:submit.prevent="submit">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Shopee Upload</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                    <div class="row">
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
                                        <div>
                                            <input class="form-control m-input my-1" v-for="(image, index) in item.image_url" type="text" name="product_image[]" v-model="item.image_url[index]">
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
                                            <option v-for="(category, index) in categories" v-bind:value="index">@{{ category.category_name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-2" v-for="(selected, index) in selected_category">
                                        <select v-bind:id="'children_'+(index+1)" class="form-control" v-on:change="addSelected($event, (index+1))" required>
                                            <option value="" selected disabled>Select Category</option>
                                            <option v-bind:value="index_children" v-for="(category, index_children) in children_categories[index]"> @{{ category.category_name }} </option>
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
                                                <input type="hidden" v-bind:name="'product_attributes['+index+'][attributes_id]'" v-bind:value="attribute.attribute_id">
                                                <input v-if="attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_ATTR_TEXT ?>'" class="form-control" type="text" v-bind:name="'product_attributes['+index+'][value]'" v-bind:placeholder="attribute.attribute_name" v-bind:required="attribute.is_mandatory ? true : false">
                                                <select class="form-control"  v-if="attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_ATTR_COMBO ?>' || attribute.input_type == '<?= \Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::SHOPEE_ATTR_DROPDOWN ?>'" v-bind:name="'product_attributes['+index+'][value]'" v-bind:placeholder="attribute.attribute_name" v-bind:required="attribute.is_mandatory ? true : false">
                                                    <option value="">--Select @{{ attribute.attribute_name }}--</option>
                                                    <option v-for="option in attribute.options">@{{ option }}</option>
                                                </select>
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
                                    <input class="form-control m-input" type="number" name="product_stock" min="0">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Weight <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <input class="form-control m-input" type="number" name="product_weight" step="0.01" min="0.01">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Shipping Logistic <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <div class="col-12 mt-2 d-flex align-items-center" v-for="(logistic, index) in logistics" v-if="logistic.enabled">
                                        <div v-if="logistic.children.length > 0">
                                            <span>@{{ logistic.logistic_name }}</span>
                                            <span class="m-switch m-switch--icon m-switch--brand ml-1 d-flex align-items-center">
                                                <label>
                                                    <input type="hidden" v-bind:name="'product_logistic['+index+'][logistic_id]'" v-bind:value="logistic.logistic_id">
                                                    <label>
                                                        <input type="checkbox" v-bind:checked="logistic.preferred ? 'checked' : ''" v-bind:name="'product_logistic['+index+'][enabled]'" value="true">
                                                        <span></span>
                                                    </label>
                                                </label>
                                            </span>
                                            {{-- <div v-for="children in logistic.children" class="ml-3" v-if="selected_logistic[index] && children.enabled">
                                                <span>@{{ children.logistic_name }}</span>
                                                <span class="m-switch m-switch--icon m-switch--metal ml-1 d-flex align-items-center">
                                                    <input type="hidden" v-bind:name="'product_logistic['+index+'][logistic_id]'" v-bind:value="children.logistic_id">
                                                    <label>
                                                        <input type="checkbox" v-bind:checked="children.preferred ? 'checked' : ''" v-bind:name="'product_logistic['+index+'][enabled]'" value="true">
                                                        <span></span>
                                                    </label>
                                                </span>  
                                             </div>  --}}
                                        </div>
                                        <div v-else>
                                            <span>@{{ logistic.logistic_name }}</span>
                                            <span class="m-switch m-switch--icon m-switch--brand ml-1 d-flex align-items-center">
                                                <label>
                                                    <input type="hidden" v-bind:name="'product_logistic['+index+'][logistic_id]'" v-bind:value="logistic.logistic_id">
                                                    <label>
                                                        <input type="checkbox" v-bind:checked="logistic.preferred ? 'checked' : ''" v-bind:name="'product_logistic['+index+'][enabled]'" value="true">
                                                        <span></span>
                                                    </label>
                                                </label>
                                            </span>                             
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Preorder <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <div class="col-12">
                                        <span class="m-switch m-switch--icon m-switch--success ml-1 d-flex align-items-center">
                                            <label>
                                                <label>
                                                    <input type="checkbox" v-model="is_pre_order" v-bind:name="'is_pre_order'" value="true">
                                                    <span></span>
                                                </label>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="col-12" v-if="is_pre_order">
                                        <input class="form-control m-input" type="text" name="days_to_ship" placeholder="Days To Ship">
                                    </div>
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
              </div>
              {{ csrf_field() }}
              <input type="hidden" class="form-control m-input" name="shop_id" value="{{getSettingConfig('shopee_id')}}">
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