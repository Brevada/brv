import moment from "moment";

/**
 * Converts an array of unix timestamps into an array of dates containing
 * individual date components, such as year, month, day, etc...
 *
 * @param   {integer[]} arrSeconds Input seconds array.
 * @returns {object[]}
 */
const parseSeconds = arrSeconds => {
    return arrSeconds.map(seconds => {
        const date = new Date(parseInt(seconds) * 1000);

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
};

/**
 * Finds the first formatter index which yields unique formatted values.
 * @param   {string[]} keys Formatter keys for use in the formats lookup.
 * @param   {object[]} dFmts An array of dates containing date components.
 * @param   {number} [i=0] The current index to check.
 * @returns {number}
 */
const findUniqueFormatter = (keys, dFmts, i = 0) => {
    if (i < keys.length) {
        const uniques = new Set(dFmts.map(s => s.comps[i]));

        if (uniques.size < dFmts.length) {

            /* Date property duplicates found across data points, advance format cursor.
             * We advance until no duplicates are found. I.e. no labels in their purest form
             * are identical.
             */

            return findUniqueFormatter(keys, dFmts, ++i);
        }
    }

    return Math.min(i, keys.length - 1);
};

const DateCluster = {

    /**
     * Gets an array of labels from dates and formatter rules.
     *
     * @param   {number[]} dates An array of unix timestamps to translate to labels.
     * @param   {object} formats A map of formatters to apply to the timestamps.
     * @returns {string[]}
     */
    getLabels (dates, formats) {
        const data = parseSeconds(dates);

        formats = Object.assign({
            year: "[']YY",
            month: "MMM",
            day: "MMM D",
            hour: "h a",
            minute: "h:mm a"
        }, formats || {});

        const keys = Object.keys(formats),
            formatCursor = findUniqueFormatter(keys, data);

        const format = formats[keys[formatCursor]];

        if (typeof format === "function") {
            return data.map(s => format(moment.unix(s.seconds)));
        }

        return data.map(s => moment.unix(s.seconds).format(format));
    }
};

export { DateCluster };
