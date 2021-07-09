<div class="tab-pane" id="gallery" role="tabpanel">
    <div class="form-group m-form__group d-flex">
        <div class="col">
            <div v-for="(item, index) in (components)">
                <div class="d-flex my-1">
                    <div class="col-md-10">
                        <div class="input-group">
                           <span class="input-group-btn">
                             <a v-bind:data-input="'input-photo-'+(index)" v-bind:data-index="index" v-bind:data-name="'photo'" v-bind:data-preview="'image-photo-'+(index)" class="btn btn-file btn-accent m-btn m-btn--air m-btn--custom lfm-input">
                               <i class="fa fa-picture-o"></i> Choose
                             </a>
                           </span>
                           <input v-bind:id="'input-photo-'+(index)" class="form-control file-input" v-bind:data-index="index" v-bind:data-name="'photo'" type="text" v-bind:name="'meta[gallery]['+(index)+'][photo]'" v-model="components[index]['photo']" required readonly>
                        </div>
                       <img v-bind:id="'image-photo-'+(index)" style="margin-top:15px;max-height:100px;" v-bind:src="components[index]['photo'] != null ? window.storage_url+components[index]['photo'] : ''">
                    </div>
                    <div class="col">
                        <button type="button" class="btn m-btn--pill btn-metal" v-on:click="removeComponent(index)"><span><i class="fa fa-minus"></i></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group m-form__group d-flex">
        <div class="col-md-6 offset-md-4">
            <button type="button" class="btn btn-success" v-on:click="addComponent">Add Photo</button>
        </div>
    </div>
</div>

@push('page_script_js')
  <script type="text/javascript">
    var Gallery = new Vue({
        mixins: [componentMixin],
        el: "#gallery",
        data: {
            components: {!! old('meta.gallery') ? json_encode(old('meta.gallery')) : (!empty($post) && !empty($post->postMeta->where('meta_key', 'gallery')->first()) ? json_encode($post->postMeta->where('meta_key', 'gallery')->first()->meta_value) : json_encode(array())) !!},
        },
    });
  </script>
@endpush