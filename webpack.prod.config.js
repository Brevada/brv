var webpack = require('webpack');
var LodashModuleReplacementPlugin = require('lodash-webpack-plugin');
var devConfig = require('./webpack.config');

module.exports = Object.assign(devConfig, {
    'plugins': [
        new webpack.optimize.CommonsChunkPlugin({
            /* Don't add content to vendor and globals. */
            names: ['vendor'],
            minChunks: Infinity
        }),
        new webpack.optimize.CommonsChunkPlugin('chunks'),
        new webpack.optimize.UglifyJsPlugin(),
        new webpack.optimize.AggressiveMergingPlugin(),
        new webpack.optimize.OccurrenceOrderPlugin(),
        new LodashModuleReplacementPlugin({
            collections: true,
            shorthands: true,
            paths: true
        })
    ]
});
