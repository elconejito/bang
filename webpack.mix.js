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

mix.setPublicPath(path.normalize('public/assets'))
  .setResourceRoot('../');

mix.js('resources/assets/js/app.js', 'js')
  .extract(['bootstrap', 'popper.js', 'dropzone'])
  .sass('resources/assets/sass/app.scss', 'css')
  .autoload({
    'jquery': ['$', 'window.jQuery', 'jQuery'],
    'popper.js/dist/umd/popper.js': ['Popper'],
    'dropzone': ['Dropzone']
  });

mix.copy( 'resources/assets/images', 'public/assets/images', false );
