const webpack = require("webpack");
const LodashModuleReplacementPlugin = require("lodash-webpack-plugin");
const devConfig = require("./webpack.config");

module.exports = Object.assign(devConfig, {
    "plugins": [
        new webpack.optimize.CommonsChunkPlugin({
            /* Don't add content to vendor and globals. */
            names: ["vendor"],
            minChunks: Infinity
        }),
        new webpack.optimize.CommonsChunkPlugin("chunks"),
        new webpack.optimize.UglifyJsPlugin(),
        new webpack.optimize.AggressiveMergingPlugin(),
        new webpack.optimize.OccurrenceOrderPlugin(),
        new LodashModuleReplacementPlugin({
            collections: true,
            shorthands: true,
            paths: true
        })
    ],
    devtool: false,
    performance: {
        hints: false
    }
});
