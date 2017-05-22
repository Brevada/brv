/* eslint-disable no-console */

const { execSync } = require("child_process");
const path = require("path");
const fs = require("fs");

console.log("Running PHPLoc for backend analysis...");
console.log("");

const phplocPath = path.join(".", "vendor", "bin", "phploc");

if (!fs.existsSync(phplocPath)) {
    console.error("PHPLoc is not installed. Please run 'composer update'.");
} else {
    const log = "--log-xml " + path.join(".", "docs", "phploc", "phploc.xml");

    const exclusions = [
        "docs",
        "legacy",
        "vendor",
        "scripts",
        "node_modules"
    ].map(f => `--exclude=${f}`).join(" ");

    const args = [
        "--count-tests",
        "--progress",
        log,
        exclusions
    ].join(" ") + " ";

    execSync(phplocPath + " " + args + " -- .", { stdio: "inherit" });
}
