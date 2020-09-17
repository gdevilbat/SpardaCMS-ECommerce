const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('js/scrapper.js', '../../resources/views/admin/v_1/js/scrapper.js')
    .js('js/shopee.js', '../../resources/views/admin/v_1/js/shopee.js')
    .js('js/tokopedia.js', '../../resources/views/admin/v_1/js/tokopedia.js')
    .options({
      processCssUrls: false
   });