const Filter = {
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

    toLabel: key => Filter.mapping[key].label,
    toDays: key => Filter.mapping[key].days,
    toPoints: key => Filter.mapping[key].points
};

export { Filter };
