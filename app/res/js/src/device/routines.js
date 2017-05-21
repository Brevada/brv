/* global brv device */
/* eslint-disable no-console */

/**
 * Starts all routines.
 * @returns {Routine}
 */
module.exports = function() {
    const routines = {};

    /**
     * Creates a routine which invokes a callback in a specified number
     * of seconds.
     * @param {function} callback [description]
     * @param {number}   interval Duration between callback invocations, in
     *                            seconds.
     * @returns {void}
     */
    function Routine (callback, interval) {
        let _tmr = undefined;

        /**
         * Starts the routine.
         * @returns {Routine}
         */
        this.start = () => {
            _tmr = setTimeout(this.execute, interval * 1000);

            return this;
        };

        /**
         * Invokes the callback, and then restarts the routine.
         * @returns {Routine}
         */
        this.execute = () => {
            clearTimeout(_tmr);

            Promise
            .resolve(callback())
            .catch(() => false)
            .then(() => {
                this.start();
            });

            return this;
        };

        /**
         * Stops the routine.
         * @returns {void}
         */
        this.stop = () => clearTimeout(_tmr);
    }

    const ajax = brv.interceptor || (() => {
        throw new Error("Unimplemented");
    });

    /**
     * Status routine, announcing tablet's state to the server.
     * @param {Routine}
     */
    routines.Status = (new Routine(() => {
        return brv.env.isOnline().then(() => ajax({
            method: "POST",
            url: brv.env.API_URL + "/device/announce",
            data: {
                battery: brv.env.getBattery(),
                position: brv.env.getPosition(),
                stored_data_count: brv.env.getDBData()
                                          .get("payloads")
                                          .size()
                                          .value(),
                device_version: device.version,
                device_model: device.model,
                software_version: brv.env.getDBConfig()
                                         .get("version")
                                         .value()
            }
        }, true));
    }, 60 * 4)).execute();

    /**
     * Checks for new commands to execute.
     * @param {Routine}
     */
    routines.Commands = (new Routine(() => {
        return brv.env.isOnline().then(() => ajax({
            method: "GET",
            url: brv.env.API_URL + "/device/commands", /* data.actions is an ordered array*/
            params: {}
        }).then(resp => brv.env.execute(resp.data.actions || [])));
    }, 60)).execute();

    /**
     * Flushes cached data to server if connection is available.
     * @param {Routine}
     */
    routines.Offline = (new Routine(() => {
        return brv.env.isOnline().then(() => {
            const offlineCnt = brv.env.getDBData()
                                      .get("payloads")
                                      .size()
                                      .value();

            if (offlineCnt > 0) {

                /* Bit of a race condition here... */
                const payloads = brv.env.getDBData()
                                        .get("payloads")
                                        .value();

                console.log("Offline Payloads: " + payloads.length);

                let prom = Promise.resolve();

                payloads.forEach(p => {
                    prom = prom.then(() => ajax(p));
                });

                return brv.env.getDBData()
                              .set("payloads", [])
                              .write()
                              .then(prom);
            }

            return Promise.resolve();
        });
    }, 120)).execute();

    return routines;
};
