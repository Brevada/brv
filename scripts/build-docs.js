/* eslint-disable no-console */

const { execSync } = require("child_process");
const path = require("path");
const fs = require("fs");

console.log("Starting PHPDox...");
console.log("");

const phpdoxPath = path.join(".", "vendor", "bin", "phpdox");

if (!fs.existsSync(phpdoxPath)) {
    console.error("PHPDox is not installed. Please run 'composer update'.");
} else {
    execSync(phpdoxPath, { stdio: "inherit" });
}
