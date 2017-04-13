const webpack = require('webpack');
const LodashModuleReplacementPlugin = require('lodash-webpack-plugin');
const path = require('path');
const moment = require('moment');
const fs = require('fs-extra');
const del = require('del');

const config = {
    entry: {
        /* We enforce ordering by numbering entries. */
        '0_vendor': ['moment', 'babel-polyfill', 'classlist-polyfill'],
        '1_feedback_brv': path.join(__dirname, '..', 'app', 'res', 'js', 'src', 'global', 'feedback.js'),
        '2_feedback_view': path.join(__dirname, '..', 'app', 'res', 'js', 'src', 'views', 'feedback.js')
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
            path.join(__dirname, '..', 'app', 'res', 'js', 'src'),
            'node_modules'
        ],
        extensions: ['.js']
    },
    'plugins': [
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: JSON.stringify('production')
            }
        }),
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

let dest = path.join(__dirname, '..', 'app', 'resp', 'feedback');
del.sync([`${dest}/**`, `!${dest}`, `!**.gitkeep`]);

compiler.run((err, stats) => {
    if (err) {
        console.error(err);
    } else {
        console.log('Hash: ' + stats.hash);
        console.log('Duration: ' + moment.duration(stats.endTime - stats.startTime).asSeconds() + 's');
    }

    console.log('');
    console.log('Copying CSS...');

    let src = path.join(__dirname, '..', 'app', 'res', 'css', 'dist', 'feedback');
    fs.copySync(src, dest);

    console.log("Done copying.");
});
