const execSync = require('child_process').execSync;
const path = require('path');
const fs = require('fs');

console.log("Running PHPUnit for backend tests...");
console.log("");

let phpunitPath = path.join('.', 'vendor', 'bin', 'phpunit');
if (!fs.existsSync(phpunitPath)) {
    console.error("PHPUnit is not installed. Please run 'composer update'.");
} else {
    let args = process.argv.slice(2).join(' ');
    execSync(phpunitPath + " --verbose " + args, { stdio: 'inherit' });
}
