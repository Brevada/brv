/**
 * Translation "table" from general time intervals to number of days,
 * number of data points (for use in charts), and english labels. Contains
 * functions to map between values.
 *
 * @type {Object}
 */
const Filter = {
    /**
     * The actual mapping for each time interval.
     *
     * label refers to the english display text.
     * days refers to the number of days in the interval.
     * points refers to the number of data points to display on a graph.
     *
     * @type {Object}
     */
    mapping: {
        'TODAY': {
            label: 'Today',
            days: 1,
            points: 8
        },
        'PAST_WEEK': {
            label: 'Past Week',
            days: 7,
            points: 7
        },
        'PAST_MONTH': {
            label: 'Past Month',
            days: 30,
            points: 4
        },
        'PAST_6_MONTH': {
            label: 'Past 6 Months',
            days: 30*6,
            points: 6
        },
        'PAST_YEAR': {
            label: 'Past Year',
            days: 365,
            points: 12
        },
        'ALL_TIME': {
            label: 'All Time',
            days: 0,
            points: 5
        }
    },

    /**
     * Asserts the existence of the filter key. Throws error if key does not
     * exist. Returns the key on success.
     *
     * @param  {string} key The key to test.
     * @return {string}
     */
    ensure: key => {
        if (typeof key === 'string' && Filter.mapping.hasOwnProperty(key)) {
            return key;
        }
        throw `Invalid filter key: ${key}`;
    },

    /**
     * Gets the display text from the time interval key.
     *
     * @param  {string} key The unique time interval key, in uppercase.
     * @return {string}
     */
    toLabel: key => Filter.mapping[key].label,

    /**
     * Gets the number of days making up the interval from the time interval key.
     *
     * @param  {string} key The unique time interval key, in uppercase.
     * @return {string}
     */
    toDays: key => Filter.mapping[key].days,

    /**
     * Gets the number of data points to display from the time interval key.
     * This is mainly a suggestion of how many pieces can logically compose data
     * from the time interval, e.g. a week has 7 days, thus 1 point for each day.
     *
     * @param  {string} key The unique time interval key, in uppercase.
     * @return {string}
     */
    toPoints: key => Filter.mapping[key].points
};

export { Filter };
