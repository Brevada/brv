var webpack = require('webpack');
var LodashModuleReplacementPlugin = require('lodash-webpack-plugin');
var devConfig = require('./webpack.config');

module.exports = Object.assign(devConfig, {
    'plugins': [
        new webpack.optimize.CommonsChunkPlugin('chunks'),
        new webpack.optimize.UglifyJsPlugin(),
        new webpack.optimize.AggressiveMergingPlugin(),
        new webpack.optimize.OccurrenceOrderPlugin(),
        new LodashModuleReplacementPlugin({
            collections: true,
            paths: true
        })
    ]
});
