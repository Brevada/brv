const execSync = require('child_process').execSync;
const path = require('path');
const fs = require('fs');

console.log("Running SASS...");
console.log("");

let args = [
    '--output-style compressed',
    '--include-path app/res/css/src',
    'app/res/css/src',
    '-o app/res/css/dist'
];

let pargs = ' ' + args.join(' ') + ' ';

execSync("node-sass" + pargs + " -- .", { stdio: 'inherit' });
