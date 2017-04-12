var sassInlineImage = require('sass-inline-image');
const path = require('path');

module.exports = Object.assign({}, sassInlineImage({
    base: path.join(__dirname, '..', '..', 'app', 'res', 'images')
}));
