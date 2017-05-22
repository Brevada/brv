/* global brv */
/* eslint-disable max-statements */

/**
 * Feedback page script. Acts as a layer of abstraction (like an interface),
 * between the feedback page's front-end and back-end. This allows us to
 * implement independent browser and tablet implementations.
 */

(function(undefined){
    "use strict";

    /* Declare a "namespace" container. */
    const fbk = {};

    /**
     * Gets/Sets the store identifier.
     * @param   {integer} val The new store id.
     * @returns {integer} The current store id, after setting, if applicable.
     */
    fbk.id = val => {
        fbk._id = val == null ? fbk._id : val;

        return fbk._id;
    };

    fbk.scroll = require("global/feedback/scroll")();
    fbk.session = require("global/feedback/session")();
    fbk.interceptor = undefined;

    /* Reference interceptor if available. */
    if (window.brv && window.brv.env && window.brv.env.IS_DEVICE) {
        fbk.interceptor = brv.interceptor || undefined;
    }

    /**
     * If available, returns cached configuration.
     * @returns {Promise} Resolves to cached configuration.
     */
    fbk.getConfig = () => {
        if (!(window.brv && window.brv.env && window.brv.env.IS_DEVICE)) {
            return Promise.reject();
        }

        const cached = brv.env
                          .getDBConfig()
                          .get("feedback_config", {})
                          .value();

        if (!cached) {

            /* No cache available. */
            return Promise.reject();
        }

        return Promise.resolve(cached);
    };

    /**
     * If environment is correct, cache config.
     * @param   {object} config The config to save.
     * @returns {Promise}        Resolves to cached config.
     */
    fbk.saveConfig = (config) => {
        if (!(window.brv && window.brv.env && window.brv.env.IS_DEVICE)) {
            return Promise.reject();
        }

        return Promise.resolve(
            brv.env
            .getDBConfig()
            .set("feedback_config", config || {})
            .write()
        );
    };

    /* Export to the global scope. */
    window.brv = window.brv || {};
    window.brv.feedback = fbk;
}());
