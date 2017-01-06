import moment from 'moment';

const DateCluster = {
    getLabels: function (dates, formats) {
        let size = dates.length;

        /* Date in seconds. */
        let data = dates.concat().map(seconds => {
            let date = new Date(parseInt(seconds)*1000);
            /* Year, Month, Day, Hour, Minute */
            return {
                seconds: parseInt(seconds),

                /* Break date into comparable components in order of general to narrow. */
                comps: [
                    date.getFullYear(), date.getMonth(), date.getDate(),
                    date.getHours(), date.getMinutes()
                ]
            };
        });

        formats = Object.assign({
            year: '[\']YY',
            month: 'MMM',
            day: 'MMM Do',
            hour: 'h a',
            minute: 'h:mm a'
        }, formats || {});

        let keys = ['year', 'month', 'day', 'hour', 'minute'];
        let formatCursor = 0;

        let matchColumn = c => {
            if (new Set(data.map(s => s.comps[c])).size < size) {
                /* Date property duplicates found across data points, advance format cursor.
                 * We advance until no duplicates are found. I.e. no labels in their purest form
                 * are identical.
                 */
                formatCursor++;
                return true;
            }
            return false;
        };

        for (let i = 0; i < keys.length; i++) {
            if (matchColumn(i) === false) break;
        }

        formatCursor = Math.min(formatCursor, keys.length - 1);
        let format = formats[keys[formatCursor]];

        if (typeof format === 'function') {
            return data.map(s => format(moment.unix(s.seconds)));
        } else {
            return data.map(s => moment.unix(s.seconds).format(format));
        }
    }
};

export { DateCluster };
