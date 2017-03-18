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
    fbk.interceptor = require('global/feedback/interceptor')();

    /* Export to the global scope. */
    window.brv = window.brv || {};
    window.brv.feedback = fbk;
})();
