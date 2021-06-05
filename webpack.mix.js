/* eslint-disable global-require,import/no-dynamic-require */

const mix = require('laravel-mix');

if (process.env.section) {
	require(`${__dirname}/webpack.mix.${process.env.section}.js`);
}
