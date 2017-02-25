/**
 * Feedback page script. Acts as a layer of abstraction (like an interface),
 * between the feedback page's front-end and back-end. This allows us to
 * implement independent browser and tablet implementations.
 */

(function(undefined){
    "use strict";

    /* Declare a "namespace" container. */
    let feedback = {};

    /**
     * Gets/Sets the store identifier.
     * @param  {integer} val The new store id.
     * @return {integer} The current store id, after setting, if applicable.
     */
    feedback.id = val => val === undefined ? feedback._id : feedback._id = val;

    /**
     * Submits feedback data.
     * @param  {integer} id The aspect id.
     */
    feedback.submit = id => {
        
    };


    /* Export to the global scope. */
    window.brv = window.brv || {};
    window.brv.feedback = feedback;
})();
