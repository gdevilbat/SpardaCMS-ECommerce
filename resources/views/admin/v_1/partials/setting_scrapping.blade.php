<div class="modal fade" id="setting-scrapping" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel">
  <div class="modal-dialog">
      <div class="modal-content">
          <form action="{{route('cms.setting.store')}}" method="post" accept-charset="utf-8">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Scrapping Setting</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body"> 
                  {{-- <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">URL</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control m-input" name="scrapping[url]" placeholder="https://[API Scrapping]" value="{{getSettingConfig('scrapping', 'url')}}">
                        </div>
                    </div> --}}
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label class="text-right" for="exampleInputEmail1">Suplier Sync Mode</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <div class="m-radio-inline">
                                {{-- <label class="m-radio">
                                    <input type="radio" name="scrapping[suplier_sync]" value="ajax" {{getSettingConfig('scrapping', 'suplier_sync') == 'ajax' ? 'checked' : ''}}> Ajax
                                    <span></span>
                                </label> --}}
                                <label class="m-radio">
                                    <input type="radio" name="scrapping[suplier_sync]" value="cloud" {{getSettingConfig('scrapping', 'suplier_sync') == 'cloud' ? 'checked' : ''}}> Cloud
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="m-form__group form-group row  d-md-flex">
                        <div class="ml-4 col-col-md-8">
                            <a onclick="popupWindow('{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\ShopeeController@authentication') }}', 'test', window, 800, 600);" href="javascript:void(0)" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                <span>
                                    <i class="la la-refresh"></i>
                                    <span>Authentication Shopee</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label class="text-right" for="exampleInputEmail1">Store Sync Mode</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <div class="m-radio-inline">
                                {{-- <label class="m-radio">
                                    <input type="radio" name="scrapping[store_sync]" value="ajax" {{getSettingConfig('scrapping', 'store_sync') == 'ajax' ? 'checked' : ''}}> Ajax
                                    <span></span>
                                </label> --}}
                                <label class="m-radio">
                                    <input type="radio" name="scrapping[store_sync]" value="cloud" {{getSettingConfig('scrapping', 'store_sync') == 'cloud' ? 'checked' : ''}}> Cloud
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label class="text-right" for="exampleInputEmail1">Shopee Session</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <input type="text" class="form-control" name="scrapping[shopee_session]" value="{{ getSettingConfig('scrapping', 'shopee_session') }}">
                        </div>
                    </div>
                    <div class="form-group m-form__group d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label class="text-right" for="exampleInputEmail1">Shopee None Match</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <input type="text" class="form-control" name="scrapping[shopee_none_match]" value="{{ getSettingConfig('scrapping', 'shopee_none_match') }}">
                        </div>
                    </div>
                    <hr> 
                    <div class="m-form__group form-group row  d-md-flex">
                        <div class="ml-4 col-col-md-8">
                            <a onclick="popupWindow('{{ action('\Gdevilbat\SpardaCMS\Modules\Ecommerce\Http\Controllers\LazadaController@authentication') }}', 'test', window, 800, 600);" href="javascript:void(0)" class="btn btn-danger m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air">
                                <span>
                                    <i class="la la-refresh"></i>
                                    <span>Authentication Lazada</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <hr>
                    <div class="m-form__group form-group row  d-md-flex">
                        <div class="col-md-4 d-md-flex justify-content-end py-3">
                            <label for="exampleInputEmail1">Weight Check</label>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <span class="m-switch m-switch--icon m-switch--success">
                                <label>
                                    <input type="checkbox" id="weight_check" {{getSettingConfig('scrapping', 'weight_check') == 'true' ? 'checked' : ''}}>
                                    <span></span>
                                    <input type="hidden" name="scrapping[weight_check]" value="{{getSettingConfig('scrapping', 'weight_check') == 'true' ? 'true' : 'false'}}">
                                </label>
                            </span>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
              </div>
              {{csrf_field()}}
              {{method_field('PUT')}}
          </form>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>