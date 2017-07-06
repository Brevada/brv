const webpack = require("webpack");
const glob = require("glob");
const path = require("path");

const views = {};
const viewFiles = glob.sync("./app/res/js/src/views/*.js");

for (const view of viewFiles) {
    views["views/" + path.basename(view, ".js")] = view;
}

module.exports = {
    entry: Object.assign({
        "vendor": ["moment", "babel-polyfill", "classlist-polyfill"],
        "globals/feedback": ["./app/res/js/src/global/feedback.js"]
    }, views),
    output: {
        path: path.join(__dirname, "app", "res", "js", "dist"),
        publicPath: "/js/",
        filename: "[name].js",
        chunkFilename: "[name]-[chunkhash].js"
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: "babel-loader?cacheDirectory"
            }
        ]
    },
    plugins: [new webpack.optimize.CommonsChunkPlugin("chunks")],
    cache: true,
    resolve: {
        modules: [
            path.join(__dirname, "app", "res", "js", "src"),
            "node_modules"
        ],
        extensions: [".js"]
    },
    performance: {
        hints: "warning"
    },
    devtool: "source-map",
    target: "web"
};
