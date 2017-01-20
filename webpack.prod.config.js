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
        "vendor": ['moment']
    }, views),
    output: {
        path: './app/res/js/dist',
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader?cacheDirectory'
            }
        ]
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin('chunks'),
        new webpack.optimize.UglifyJsPlugin(),
        new webpack.optimize.AggressiveMergingPlugin(),
        new webpack.optimize.OccurrenceOrderPlugin()
    ],
    cache: true,
    resolve: {
        modules: [
            path.join(__dirname, 'app', 'res', 'js', 'src'),
            'node_modules'
        ],
        extensions: ['.js']
    }
};
