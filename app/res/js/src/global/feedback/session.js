/**
 * Tracks current session.
 */
module.exports = function() {
    var session = {};

    let _token = undefined;
    let _remainingCnt = 0;

    let newToken = () => _token = require('crypto').randomBytes(16).toString('hex');
    newToken();

    session.init = () => {
        newToken();
    };

    session.complete = () => false;

    session.getRemainingCount = () => _remainingCnt;
    session.setRemainingCount = n => _remainingCnt = n;
    session.getToken = () => _token;

    return session;
};
