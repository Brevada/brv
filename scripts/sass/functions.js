var inlineImage = require("./inline-image");
const path = require("path");

module.exports = Object.assign({}, inlineImage({
    base: path.join(__dirname, "..", "..", "app", "res", "images")
}));
