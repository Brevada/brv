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
});
