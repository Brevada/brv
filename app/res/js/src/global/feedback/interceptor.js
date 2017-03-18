/**
 * Emulates "axios", saving data offline instead of immediately sending over
 * the network.
 */
module.exports = function() {
    var interceptor = function (ops){
        if (!ops.method || !ops.url) {
            return Promise.reject();
        }

        // TODO: Store in persistant storage.
        return Promise.resolve();
    };

    return interceptor;
};
