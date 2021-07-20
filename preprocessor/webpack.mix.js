const mix  = require('laravel-mix');
mix.setPublicPath('../resources/views/admin');

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

mix.js('js/scrapper.js', 'v_1/js/scrapper.js')
    .js('js/shopee.js', 'v_1/js/shopee.js')
    .js('js/tokopedia.js', 'v_1/js/tokopedia.js')
    .js('js/lazada.js', 'v_1/js/lazada.js')
    .options({
      processCssUrls: false
   });