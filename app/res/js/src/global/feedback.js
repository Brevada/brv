/**
 * Feedback page script. Acts as a layer of abstraction (like an interface),
 * between the feedback page's front-end and back-end. This allows us to
 * implement independent browser and tablet implementations.
 */

(function(undefined){
    "use strict";

    /* Declare a "namespace" container. */
    let fbk = {};

    /**
     * Gets/Sets the store identifier.
     * @param  {integer} val The new store id.
     * @return {integer} The current store id, after setting, if applicable.
     */
    fbk.id = val => val === undefined ? fbk._id : fbk._id = val;

    fbk.scroll = require('global/feedback/scroll')();
    fbk.session = require('global/feedback/session')();
    fbk.interceptor = undefined;

    /* Reference interceptor if available. */
    if (window.brv && window.brv.env && window.brv.env.IS_DEVICE) {
        fbk.interceptor = brv.interceptor || undefined;
    }

    /* If available, returns cached configuration. */
    fbk.getConfig = () => {
        if (!(window.brv && window.brv.env && window.brv.env.IS_DEVICE)) {
            return Promise.reject();
        }

        let cached = brv.env.getDBConfig().get('feedback_config', {}).value();
        if (!cached) {
            /* No cache available. */
            return Promise.reject();
        } else {
            return Promise.resolve(cached);
        }
    };

    /* If environment is correct, cache config. */
    fbk.saveConfig = (config) => {
        if (!(window.brv && window.brv.env && window.brv.env.IS_DEVICE)) {
            return Promise.reject();
        }

        return Promise.resolve(
            brv.env.getDBConfig().set('feedback_config', config || {}).write()
        );
    };

    /* Export to the global scope. */
    window.brv = window.brv || {};
    window.brv.feedback = fbk;
})();
