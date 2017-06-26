/* global brv */

/**
 * Device-specific logic.
 *
 * Private.
 */

brv.env.onReady(() => {
    "use strict";

    /* Cron-like routines. */
    require("device/routines")();

    /**
     * We keep track of idleness.
     */
    clearInterval(brv._idleTimer);
    brv._idleSeconds = 0;

    brv._idleTimer = setInterval(() => {
        brv._idleSeconds++;
    }, 1000);

    if (!brv._idleRegistered) {
        brv._idleRegistered = true;
        ["click", "mousemove", "scroll", "keypress"].forEach((event) => {
            const idleEventOptions = {
                capture: true,
                passive: true
            };

            document.addEventListener(event, () => {
                brv._idleSeconds = 0;
            }, idleEventOptions);

            window.addEventListener(event, () => {
                brv._idleSeconds = 0;
            }, idleEventOptions);
        });
    }
});
