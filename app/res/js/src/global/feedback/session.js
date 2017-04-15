/**
 * Tracks current session.
 */
module.exports = function() {
    var session = {};

    let _token = undefined;
    let _remainingCnt = 0;
    let _hasPoor = false;

    let newToken = () => _token = require('crypto').randomBytes(16).toString('hex');
    newToken();

    session.init = () => {
        newToken();
        _hasPoor = false;
    };

    session.complete = () => false;

    session.getRemainingCount = () => _remainingCnt;
    session.setRemainingCount = n => _remainingCnt = n;
    session.getToken = () => _token;
    session.hasPoor = () => _hasPoor;

    session.onSubmit = (value, ordinal) => {
        if (ordinal <= 1) {
            _hasPoor = true;
        }
    };

    return session;
};
