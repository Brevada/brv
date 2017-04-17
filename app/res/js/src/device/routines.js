/**
 * Starts all routines.
 */
module.exports = function() {
    let routines = {};

    /* interval in seconds */
    function Routine (callback, interval) {
        let _tmr = undefined;

        this.start = () => {
            _tmr = setTimeout(this.execute, interval * 1000);
            return this;
        };

        this.execute = () => {
            clearTimeout(_tmr);
            Promise.resolve(callback()).catch(() => false).then(() => {
                this.start();
            });
            return this;
        };

        this.stop = () => clearTimeout(_tmr);
    }

    const ajax = brv.interceptor || (() => {
        throw new Error("Unimplemented");
    });

    routines.Status = (new Routine(() => {
        return brv.env.isOnline().then(() => ajax({
            method: 'POST',
            url: brv.env.API_URL + '/device/announce',
            data: {
                battery: brv.env.getBattery(),
                position: brv.env.getPosition(),
                stored_data_count: brv.env.getDBData().get('payloads').size().value(),
                device_version: device.version,
                device_model: device.model,
                software_version: brv.env.getDBConfig().get('version').value()
            }
        }, true));
    }, 60 * 4)).execute();

    routines.Commands = (new Routine(() => {
        return brv.env.isOnline().then(() => ajax({
            method: 'GET',
            url: brv.env.API_URL + '/device/commands' /*data.actions is an ordered array*/
        }).then(resp => brv.env.execute(resp.data.actions || [])));
    }, 60)).execute();

    routines.Offline = (new Routine(() => {
        return brv.env.isOnline().then(() => {
            if (brv.env.getDBData().get('payloads').size().value() > 0) {
                /* Bit of a race condition here... */
                let payloads = brv.env.getDBData().get('payloads').value();
                console.log("Offline Payloads: " + payloads.length);

                let prom = Promise.resolve();
                payloads.forEach(p => {
                    prom = prom.then(() => ajax(p));
                });

                return brv.env.getDBData().set('payloads', []).write().then(prom);
            } else {
                return Promise.resolve();
            }
        });
    }, 120)).execute();

    return routines;
};
