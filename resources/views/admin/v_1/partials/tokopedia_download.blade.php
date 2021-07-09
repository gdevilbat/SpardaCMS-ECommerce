<div class="modal fade" id="modal-tokopedia-download" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel" data-dismiss="modal">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <form  id="tokopedia_form" data-action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TokopediaController@store') }}" onsubmit="TokopediaDownload.submit(event)">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Tokopedia Download</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                    <div class="row" id="tokopedia_download">
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
                                        <input class="form-control m-input" type="text" name="post[post_title]" v-model="item.product_name">
                                    </div>
                                </div>
                                 <div class="form-group m-form__group row">
                                    <label for="example-text-input" class="col-3 col-form-label">Product Slug <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    <div class="col">
                                        <input class="form-control m-input" type="text" name="post[post_slug]" v-model="item.slug" readonly>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label for="example-text-input" class="col-3 col-form-label">Product Image <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    <div class="col">
                                        <div v-for="(image, index) in item.images">
                                            <input class="form-control m-input my-1" type="text" name="product_image[]" v-model="image.urlOriginal">
                                            <img v-bind:src="item.images[index].urlOriginal" width="200" alt="">
                                            <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeImage(index)"><span><i class="fa fa-minus"></i></span></button>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <button type="button" class="btn btn-success" v-on:click="addImage">Add Photo</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Price <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <input class="form-control m-input" type="number" name="product_meta[product_price]" v-model="item.price">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Product Sale<span class="ml-1 m--font-warning" aria-required="true">(Optional)</span></label>
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control m-input money-masking" placeholder="Product Sale" name="product_meta[product_sale]" v-model="product_sale">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Product Weight<span class="ml-1 m--font-warning" aria-required="true">(Optional)</span></label>
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control m-input" placeholder="Product Weight" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::PRODUCT_WEIGHT}}]" v-model="item.product_weight" step="0.01">
                                </div>
                            </div>
                             <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Product Stock<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control m-input" placeholder="Product Stock" name="product_meta[product_stock]" v-model="item.product_stock">
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Product Condition<span class="ml-1 m--font-warning" aria-required="true">*</span></label>
                                </div>
                                <div class="col d-flex align-items-center">
                                    <div class="m-form__group form-group">
                                        <div class="m-radio-inline">
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[condition]" value="new" v-model="item.condition"> New
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[condition]" value="refurbished" v-model="item.condition"> Refurbished
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[condition]" value="used" v-model="item.condition"> Used
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Category<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                </div>
                                <div class="col">
                                    <select class="form-control m-input" name="taxonomy[category][]">
                                        @foreach ($categories as $category)
                                            <option value="{{$category->getKey()}}">{{$category->term->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Is Variant</label>
                                <div class="col">
                                    <div class="col-12">
                                        <span class="m-switch m-switch--icon m-switch--success ml-1 d-flex align-items-center">
                                            <label>
                                                <label>
                                                    <input type="checkbox" v-model="item.is_variant" value="true">
                                                    <span></span>
                                                </label>
                                            </label>
                                        </span>
                                    </div>
                                    <div v-if="item.is_variant">
                                        <div class="col-12 mb-3" v-for="(children, index) in item.children">
                                            <label>@{{ children.productName }}</label>
                                            <div class="col-12 d-flex">
                                                <div class="col">
                                                    <input  class="form-control" type="text" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][children][][product_id]" v-bind:value="children.productID" placeholder="Product ID">
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeChildren(index)"><span><i class="fa fa-minus"></i></span></button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success" v-on:click="addChildren">Add Children</button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Publish {{trans_choice('post::messages.post', 1)}}<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                </div>
                                <div class="col">
                                    <span class="m-switch m-switch--icon m-switch--danger">
                                        <label>
                                            <input type="checkbox"  name="post[post_status]">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-3 col-form-label">
                                    <label for="exampleInputEmail1">Open Comment<span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                </div>
                                <div class="col">
                                    <span class="m-switch m-switch--icon m-switch--danger">
                                        <label>
                                            <input type="checkbox"  name="post[comment_status]">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Description <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <textarea name="post[post_content]" class="form-control autosize" v-model="item.description"></textarea>
                                </div>
                            </div>
                            <input type="hidden" class="form-control m-input" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][merchant]" v-bind:value="item.store">
                            <input type="hidden" class="form-control m-input" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][slug]" v-bind:value="item.slug">
                            <input type="hidden" class="form-control m-input" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][is_variant]" v-bind:value="item.is_variant">
                            <input type="hidden" class="form-control m-input" name="meta[{{Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta::TOKPED_SUPPLIER}}][product_id]" v-bind:value="item.product_id">
                            <input type="hidden" class="form-control m-input" name="meta[meta_title]" data-target-count-text="#meta-title" v-model="item.product_name">
                        </div>
                    </div>
                    @include('ecommerce::admin.v_1.partials.variant')
              </div>
              {{ csrf_field() }}
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
              </div>
          </form>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>