<div class="modal fade" id="web-scrapping" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <form action="{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\TokopediaController@getData') }}" method="post" accept-charset="utf-8" id="data-web-scrapping">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Web Scrapping</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Store</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" name="store_name" placeholder="Store Name" value="">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Page</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" min="1" class="form-control m-input" name="page" placeholder="Pagination Number" value="">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Limit</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" min="1" class="form-control m-input" name="limit" placeholder="Default: 80" value="">
                        </div>
                    </div>
                    <div id="data_scrapping" class="row" v-cloak>
                        <div class="col-12">
                            <span v-for="item in items">
                                <a v-bind:href="item.url" target="_blank"><span class="badge badge-info">@{{ item.product_name }}</span></a>
                                <a href="javascript:void(0)" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--outline-2x my-1" v-on:click="setShopeeUploadItem(item)">
                                    <i class="la la-download"></i>
                                </a>&nbsp;|&nbsp;
                            </span>
                            <span v-if="window.objSize(items) > 0">| @{{ window.objSize(items) }} Item</span>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                  <button type="button" id="submit-data-scrapping" class="btn btn-primary">Scan</button>
                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
              </div>
          </form>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>