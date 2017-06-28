/**
 * Takes and modified from https://github.com/JosephClay/sass-inline-image
 * and https://coderwall.com/p/fhgu_q/inlining-images-with-gulp-sass
 */

const fs = require("fs");
const path = require("path");
const { types } = require("node-sass");

const dataUrl = {
    /**
     * Converts an image buffer to a data url.
     * @param   {[type]} buffer [description]
     * @returns {string}
     */
    svg (buffer) {
        return (
            `data:image/svg+xml;utf8,${
                buffer.toString()
                    .replace(/#/g, "%23")
                    .replace(/[\n\r"]/g, "")
            }`
        );
    },

    /**
     * Converts image buffer to data url.
     * @param   {[type]} buffer [description]
     * @param   {[type]} ext    [description]
     * @returns {string}
     */
    img (buffer, ext) {
        return `data:image/${ext};base64,${buffer.toString("base64")}`;
    }
};

module.exports = (options) => {
    options = options || {};

    const base = options.base || process.cwd();
    const name = options.name || "inline-image";

    return {
        [`${name}($file)`]: file => {
            const filePath = path.resolve(base, `./${file.getValue()}`);
            const ext = path.extname(filePath).substring(1) || "";
            const data = fs.readFileSync(filePath);

            const dataEncoder = ({
                svg: dataUrl.svg
            })[ext] || dataUrl.img;

            return types.String(`"${dataEncoder(new Buffer(data), ext)}"`);
        }
    };
};
