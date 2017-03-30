/**
 * Tracks current session.
 */
module.exports = function() {
    var session = {};

    session.new = () => ();

    return session;
};
