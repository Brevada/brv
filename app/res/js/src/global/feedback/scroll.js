module.exports = function() {
    var scroll = {};

    /* scrollY compat from MDN. */
    const supportPageOffset = window.pageXOffset != null,
        isCSS1Compat = (document.compatMode || "") === "CSS1Compat";

    const _scrollBreaks = {};
    let _scrollLock = false;

    /**
     * Starts monitoring for Y scrolls more than this breakpoint. Not incl.
     * @param   {object} obj The monitor properties.
     * @param   {string} obj.key Monitor key.
     * @param   {number} obj.px Threshold.
     * @returns {void}
     */
    scroll.monitor = obj => {
        if (!_scrollBreaks.hasOwnProperty(obj.key)) _scrollBreaks[obj.key] = obj;
    };

    /**
     * Locks or Unlocks the "scroll" ability of the user.
     * @param   {boolean} [b=true] Whether to lock the scroll.
     * @returns {void}
     */
    scroll.lock = (b = true) => {
        const p = b ? "add" : "remove";

        document.body.classList[p]("lock-scroll");
    };

    /* Bind events. */
    window.addEventListener("scroll", () => { // eslint-disable-line complexity
        if (_scrollLock) return;
        _scrollLock = true;

        // eslint-disable-next-line no-nested-ternary
        const y = supportPageOffset ?
                window.pageYOffset :
                isCSS1Compat ?
                document.documentElement.scrollTop : document.body.scrollTop;

        for (const label of Object.keys(_scrollBreaks)) {
            const clas = `scroll-${label}`;

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
            } else if (document.body.classList.contains(clas)) {

                /* Remove class from body. */
                document.body.classList.remove(clas);
            }
        }

        _scrollLock = false;
    });

    return scroll;
};
