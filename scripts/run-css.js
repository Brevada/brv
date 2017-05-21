/* eslint-disable no-console */

const { execSync } = require("child_process");
const path = require("path");

console.log("Running SASS...");
console.log("");

const args = [
    "--output-style compressed",
    "--functions " + path.join(__dirname, "sass/functions.js"),
    "--importer " + path.join(__dirname, "sass/importer.js"),
    "--include-path app/res/css/src",
    "app/res/css/src",
    "-o app/res/css/dist"
];

const pargs = " " + args.join(" ") + " ";

execSync("node-sass" + pargs + " -- .", { stdio: "inherit" });
