const webpack = require('webpack');
const LodashModuleReplacementPlugin = require('lodash-webpack-plugin');
const path = require('path');
const moment = require('moment');

const config = {
    entry: {
        feedback: path.join(__dirname, '..', 'app', 'res', 'js', 'src', 'views', 'feedback.js')
    },
    output: {
        path: path.join(__dirname, '..', 'app', 'resp', 'feedback'),
        publicPath: '/js/',
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
    cache: true,
    resolve: {
        modules: [
            path.join(__dirname, 'app', 'res', 'js', 'src'),
            'node_modules'
        ],
        extensions: ['.js']
    },
    'plugins': [
        new webpack.optimize.UglifyJsPlugin(),
        new LodashModuleReplacementPlugin({
            collections: true,
            shorthands: true,
            paths: true
        })
    ]
};

let compiler = webpack(config);

/* Production config. */

compiler.run((err, stats) => {
    if (err) {
        console.error(err);
    } else {
        console.log('Hash: ' + stats.hash);
        console.log('Duration: ' + moment.duration(stats.endTime - stats.startTime).asSeconds() + 's');
    }
});
