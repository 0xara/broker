const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .setPublicPath('public/web-assets')
    .setResourceRoot('public/web-assets');

mix.webpackConfig({
    output: {
        /** for require.ensure link
         https://github.com/webpack/webpack/issues/2000
         and is different with mix publicPath
         mix publicPath is webpack output.path config* */
        publicPath: '/web-assets/',

        // https://github.com/JeffreyWay/laravel-mix/issues/936
        // for vue async components
        chunkFilename: mix.inProduction() ? '[name].js?id=[chunkhash]' : '[name].js'
    },
});

/*mix.postCss('resources/css/app.css', './css', [

    ]);*/

require('./resources/mix/user/sass/user-sass')(mix,{});

mix.options({
    postCss: [ tailwindcss('./resources/tailwind/user-tailwind.config.js') ],
});

mix.js('resources/js/user/app.js', './js').vue().version();
