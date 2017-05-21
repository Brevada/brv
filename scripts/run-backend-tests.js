/* eslint-disable no-console */

const { execSync } = require("child_process");
const path = require("path");
const fs = require("fs");
const chalk = require("chalk");

console.log("Running PHPUnit for backend tests...");
console.log("");

const phpunitPath = path.join(".", "vendor", "bin", "phpunit");

if (!fs.existsSync(phpunitPath)) {
    console.error("PHPUnit is not installed. Please run 'composer update'.");
} else {
    // eslint-disable-next-line no-magic-numbers
    const args = process.argv.slice(2).join(" ");

    try {
        execSync(phpunitPath + " --verbose " + args, {stdio: "inherit"});
        chalk.green("All tests have passed.");
    } catch (e) {
        chalk.red("At least 1 test failed.");
    }
}
