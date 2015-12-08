var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.scripts('./node_modules/bootstrap/dist/js/bootstrap.js', 'public/assets/js')
    mix.sass('app.scss', 'public/assets/css');
    mix.copy('node_modules/font-awesome/fonts', 'public/assets/fonts');
});
