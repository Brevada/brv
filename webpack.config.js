var webpack = require('webpack');
var glob = require('glob');
var path = require('path');

var views = {};
var viewFiles = glob.sync('./app/res/js/src/views/*.js');
for (let view of viewFiles) {
    views['views/' + path.basename(view, '.js')] = view;
}

module.exports = {
    entry: Object.assign({
        "vendor": ['jquery', 'moment']
    }, views),
    output: {
        path: './app/res/js/dist',
        filename: '[name].js'
    },
    module: {
        loaders: [{
            test: /\.js$/,
            exclude: /node_modules/,
            loader: 'babel-loader?cacheDirectory'
        }]
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin('chunks.js')
    ],
    cache: true,
    resolve: {
        root: path.resolve('./app/res/js/src'),
        extensions: ['', '.js']
    }
};
