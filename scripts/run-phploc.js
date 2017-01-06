const execSync = require('child_process').execSync;
const path = require('path');
const fs = require('fs');

console.log("Running PHPLoc for backend analysis...");
console.log("");

let phplocPath = path.join('.', 'vendor', 'bin', 'phploc');
if (!fs.existsSync(phplocPath)) {
    console.error("PHPLoc is not installed. Please run 'composer update'.");
} else {
    let log = "--log-xml " + path.join('.', 'docs', 'phploc', 'phploc.xml');

    let exclusions = ['docs', 'legacy', 'vendor', 'scripts', 'node_modules']
                     .map(f => `--exclude=${f}`).join(' ');

    let args = ' ' + ['--count-tests', '--progress', log, exclusions].join(' ') + ' ';

    execSync(phplocPath + args + " -- .", { stdio: 'inherit' });
}
