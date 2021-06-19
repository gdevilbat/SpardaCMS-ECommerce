<div class="modal fade" id="modal-tokopedia-download" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel" data-dismiss="modal">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <form  id="tokopedia_download" data-action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TokopediaController@store') }}" v-on:submit.prevent="submit">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Tokopedia Download</h5>
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
                                        <input class="form-control m-input" type="text" name="post[post_title]" v-model="item.product_name">
                                    </div>
                                </div>
                                 <div class="form-group m-form__group row">
                                    <label for="example-text-input" class="col-3 col-form-label">Product Slug <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    <div class="col">
                                        <input class="form-control m-input" type="text" name="post[post_slug]" v-model="item.slug">
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label for="example-text-input" class="col-3 col-form-label">Product Image <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                    <div class="col">
                                        <div>
                                            <input class="form-control m-input my-1" v-for="(image, index) in item.images" type="text" name="product_image[]" v-model="item.images[index].urlOriginal">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="example-text-input" class="col-3 col-form-label">Product Price <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <input class="form-control m-input" type="number" name="product_meta[product_price]" v-model="item.price_int">
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
                                    <label for="exampleInputEmail1">Product Availablity<span class="ml-1 m--font-warning" aria-required="true">*</span></label>
                                </div>
                                <div class="col d-flex align-items-center">
                                    <div class="m-form__group form-group">
                                        <div class="m-radio-inline">
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[availability]"  value="in stock" v-model="product_avalability"> In Stock
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[availability]"  value="out of stock" v-model="product_avalability"> Out Of Stock
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[availability]"  value="preorder" v-model="product_avalability"> Preorder
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[availability]"  value="available for order" v-model="product_avalability"> Available For Order
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[availability]"  value="discontinued" v-model="product_avalability"> Discontinued
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
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
                                                <input type="radio" name="product_meta[condition]" value="new" v-model="product_condition"> New
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[condition]" value="refurbished" v-model="product_condition"> Refurbished
                                                <span></span>
                                            </label>
                                            <label class="m-radio">
                                                <input type="radio" name="product_meta[condition]" value="used" v-model="product_condition"> Used
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group d-flex px-0 flex-wrap">
                                <div class="col-4 d-flex justify-content-end py-3">
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
                                <label for="example-text-input" class="col-3 col-form-label">Product Description <span class="ml-1 m--font-danger" aria-required="true">*</span></label>
                                <div class="col">
                                    <textarea name="post[post_content]" class="form-control autosize" v-model="item.description"></textarea>
                                </div>
                            </div>
                            <input type="hidden" class="form-control m-input" placeholder="Tokopedia Store" name="meta[tokopedia_supplier]" v-bind:value="item.store">
                            <input type="hidden" class="form-control m-input" placeholder="Tokopedia Slug" name="meta[tokopedia_source]" v-bind:value="item.slug">
                        </div>
                    </div>
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