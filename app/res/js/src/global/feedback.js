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


    /* == Scroll Monitoring == */

    /* scrollY compat from MDN. */
    let supportPageOffset = window.pageXOffset !== undefined;
    let isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");

    let _scrollBreaks = {};
    let _scrollLock = false;

    /**
     * Starts monitoring for Y scrolls more than this breakpoint. Not incl.
     * @param  {object} obj
     * @param {string} obj.key
     * @param {number} obj.px
     */
    feedback.scrollClass = obj => {
        if (!_scrollBreaks.hasOwnProperty(obj.key)) {
            _scrollBreaks[obj.key] = obj;
        }
    };

    window.addEventListener('scroll', e => {
        if (_scrollLock) return;
        _scrollLock = true;

        let y = supportPageOffset ?
                window.pageYOffset : isCSS1Compat ?
                document.documentElement.scrollTop : document.body.scrollTop;

        for (let label of Object.keys(_scrollBreaks)) {
            let clas = `scroll-${label}`;
            if (y > _scrollBreaks[label].px) {
                /* If set, require a certain remainder to be present before applying,
                 * scroll class. */
                if (Math.max(
                        document.body.scrollHeight,
                        document.body.offsetHeight,
                        document.documentElement.clientHeight,
                        document.documentElement.scrollHeight,
                        document.documentElement.offsetHeight
                    ) - window.innerHeight > (_scrollBreaks[label].remainder || -1)) {
                        /* Add class to body. */
                        document.body.classList.add(clas);
                }
            } else if(document.body.classList.contains(clas)) {
                /* Remove class from body. */
                document.body.classList.remove(clas);
            }
        }

        _scrollLock = false;
    });

    /**
     * Locks or Unlocks the "scroll" ability of the user.
     * @param  {boolean} b
     */
    feedback.lockScroll = b => {
        if (b) {
            document.body.classList.add('lock-scroll');
        } else {
            document.body.classList.remove('lock-scroll');
        }
    }

    /* == End of Scroll Monitoring == */

    /* Export to the global scope. */
    window.brv = window.brv || {};
    window.brv.feedback = feedback;
})();
