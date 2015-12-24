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
    mix.scripts([
            '../bower/jquery/dist/jquery.js',
            '../bower/bootstrap/dist/js/bootstrap.js'
        ],
        'public/assets/js/vendor.js');
    mix.scripts('app.js', 'public/assets/js/app.js');
    mix.sass('app.scss', 'public/assets/css/app.css');
    mix.copy('assets/bower/font-awesome/fonts', 'public/assets/fonts');
});
