/* eslint-disable max-statements */

/**
 * Tracks current session.
 * @returns {object}
 */
module.exports = function() {
    const session = {};

    let _token = null,
        _remainingCnt = 0,
        _hasPoor = false;

    /**
     * Generates a new session token.
     * @returns {void}
     */
    const newToken = () => {
        _token = require("crypto").randomBytes(16)
                                  .toString("hex");

        return _token;
    };

    session.init = () => {
        newToken();
        _hasPoor = false;
    };

    session.complete = () => false;

    session.getRemainingCount = () => _remainingCnt;
    session.setRemainingCount = n => {
        _remainingCnt = n;

        return _remainingCnt;
    };

    session.getToken = () => _token;
    session.hasPoor = () => _hasPoor;

    session.onSubmit = (value, ordinal) => {
        if (ordinal <= 1) {
            _hasPoor = true;
        }
    };

    newToken();

    return session;
};
