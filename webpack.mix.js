let mix = require('laravel-mix');

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

mix.setPublicPath('public/assets')
    .js('resources/assets/js/app.js', 'public/assets/js')
    .extract(['bootstrap', 'popper.js'])
    .sass('resources/assets/sass/app.scss', 'public/assets/css')
    .options({
        processCssUrls: false
    })
    .autoload({
        'jquery': ['$', 'window.jQuery', 'jQuery'],
        'popper.js': ['Popper']
    });
