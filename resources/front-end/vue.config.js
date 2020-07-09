const webpack = require('webpack');
var path = require('path');

module.exports = {
  configureWebpack: {
    plugins: [
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        Popper: ['popper.js', 'default'],
      }),
    ],
    resolve: {
      alias: {
        components: path.resolve(__dirname, 'src/components'),
        layouts: path.resolve(__dirname, 'src/layouts'),
        mixins: path.resolve(__dirname, 'src/mixins'),
        pages: path.resolve(__dirname, 'src/pages'),
      },
    },
  },

  devServer: {
    proxy: 'https://bang.test',
    https: true,
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: '../../public/assets/app',

  publicPath: process.env.NODE_ENV === 'production'
    ? '/assets/app/'
    : '/',

  // modify the location of the generated HTML file. Original path is the public folder
  indexPath: process.env.NODE_ENV === 'production'
    ? '../../../resources/views/app.blade.php'
    : 'index.html'
};
