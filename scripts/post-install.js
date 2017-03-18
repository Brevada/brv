const execSync = require('child_process').execSync;

try {
    if (process.env.NODE_ENV === 'production') {
        execSync("composer install --no-dev", {stdio: 'inherit'});
    } else {
        execSync("composer install", {stdio: 'inherit'});
    }
} catch (e) {
    console.error("Unable to run composer. Make sure 'composer' is in your local environment.");
}
