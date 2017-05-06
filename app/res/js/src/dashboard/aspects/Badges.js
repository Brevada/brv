import React from 'react';
import PropTypes from 'prop-types';

import { Mood } from 'utils/Mood';
import classNames from 'classnames';

/**
 * An individual badge item.
 *
 * Displays a number, a label, and a colour dependent on a number.
 *
 * @param {object} props
 * @param {boolean} props.change Is the number a delta and can thus be +/-?
 * @param {number} props.value The number to display (before formatting).
 * @param {number} props.mood The number to base the mood (colour) on.
 * @param {function(number)} props.formatter A formatter function applied to
 * the value before display.
 * @param {string} props.className Optional class name.
 * @param {boolean} props.percent Should the number be displayed as a percentage?
 *
 * @param {string} label The text to display below the number.
 */
const Badge = props => {
    /* If value is a delta, base mood on scale from [-100, 100]. */
    let moodClass = props.change ? Mood(props.value, -100) : Mood(props.value);
    /* If mood value is supplied, use this value instead. */
    moodClass = typeof props.mood === 'undefined' ? moodClass : Mood(props.mood);

    /* Apply formatting to value. */
    const value = props.formatter && props.value !== null ? props.formatter(props.value) : props.value;

    /* Placeholder to display if value is null. */
    const nullHolder = (
        <div className={'value loading ' + Mood(50) + ' ' + (props.className || '')}>
            N/A
        </div>
    );

    /* The final formatted value to display, with proper plus-minus sign and percent. */
    const displayValue = value !== null && (
        (props.change ?
            (value > 0 ? '+' : (value == 0 ? '' : '-'))
        : '') + Math.abs(value) + (props.percent ? '%' : '')
    );

    return (
        <div className={'badge ' + (props.className || '')}>
            { value === null && nullHolder }
            { value !== null && (
                <div className={'value ' + moodClass + ' ' + (props.className || '')}>
                    {displayValue}
                </div>
            ) }
            <div className='label'>{props.label}</div>
        </div>
    );
};

/**
 * Collection of badges used to display a quick summary of overall stats.
 */
class Badges extends React.Component {

    static propTypes = {
        /* Display badges inline with text. */
        inline: PropTypes.bool,

        /* 4 possible stats are displayed (all numeric). */
        average: PropTypes.number,
        responses: PropTypes.number,
        to_all_time: PropTypes.number,
        to_industry: PropTypes.number
    };

    static defaultProps = {
        inline: false,
        industry: true
    };

    constructor() {
        super();

        this.fmtPercent = ::this.fmtPercent;
        this.fmtResponses = ::this.fmtResponses;
    }

    /**
     * Rounds value to 1 decimal place.
     */
    fmtPercent(x) {
        return +x.toFixed(1);
    }

    /**
     * Rounds value to 1 decimal place and abbreviates >thousand with a "k" suffix.
     */
    fmtResponses(r) {
        let length = r.toString().length;
        if (length <= 4) {
            return r;
        } else {
            return (+(r/1000).toFixed(1)) + 'k';
        }
    }

    render() {
        return (
            <div className={classNames('badges', { inline: this.props.inline })}>
                <Badge
                    value={this.props.average}
                    label={'Average'}
                    change={false}
                    percent={true}
                    formatter={this.fmtPercent}
                />
                <Badge
                    value={this.props.responses}
                    label={'Responses'}
                    change={false}
                    percent={false}
                    mood={100}
                    className='responses'
                    formatter={this.fmtResponses}
                />
                { this.props.filter !== 'ALL_TIME' && (
                    <Badge
                        value={this.props.to_all_time}
                        label={'To All Time'}
                        change={true}
                        percent={true}
                        formatter={this.fmtPercent}
                    />
                ) }
                { this.props.industry && (
                    <Badge
                        value={this.props.to_industry}
                        label={'To Industry'}
                        change={true}
                        percent={true}
                        formatter={this.fmtPercent}
                    />
                ) }
            </div>
        );
    }
}

export { Badges };
