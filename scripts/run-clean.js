const path = require('path');
const fs = require('fs');
const del = require('del');

console.log("Cleaning directory...");
console.log("");

let toClean = [
    { name: 'JS', path: path.join(__dirname, '..', 'app', 'res', 'js', 'dist') },
    { name: 'CSS', path: path.join(__dirname, '..', 'app', 'res', 'css', 'dist') },
];

for (let rule of toClean) {
    if (!fs.existsSync(rule.path)) {
        console.warn(`${rule.name} has an invalid path. Skipping...`);
        continue;
    }

    console.log(`Cleaning ${rule.name}...`);

    let deleted = del.sync([`${rule.path}/**`, `!${rule.path}`, `!**.gitkeep`]);
    console.log(deleted);
}

console.log("Done cleaning.");
