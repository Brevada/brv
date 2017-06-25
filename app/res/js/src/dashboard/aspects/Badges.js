import React, { Component } from "react";
import PropTypes from "prop-types";

import { mood } from "utils/Mood";
import classNames from "classnames";

/**
 * An individual badge item.
 *
 * Displays a number, a label, and a colour dependent on a number.
 *
 * @param   {object} props React props
 * @param   {boolean} props.change Is the number a delta and can thus be +/-?
 * @param   {number} props.value The number to display (before formatting).
 * @param   {number} props.mood The number to base the mood (colour) on.
 * @param   {function(number)} props.formatter A formatter function applied to
 * the value before display.
 * @param   {string} props.className Optional class name.
 * @param   {boolean} props.percent Should the number be displayed as a percentage?
 * @param   {string} props.label The text to display below the number.
 * @returns {JSX}
 */
const Badge = props => { // eslint-disable-line complexity
    /* If value is a delta, base mood on scale from [-100, 100].
     * If mood value is supplied, use this value instead. */
    const moodClass = mood(
        props.mood == null ? props.value : props.mood,
        props.mood == null && props.change ? -100 : 0
    );

    /* Apply formatting to value. */
    const value = props.formatter && props.value !== null ?
                  props.formatter(props.value) : props.value;

    /* Placeholder to display if value is null. */
    const nullHolder = value === null && (
        <div
            className={classNames("value loading", mood(50), props.className)}>
            {"N/A"}
        </div>
    );

    return (
        <div className={classNames("badge", props.className)}>
            { nullHolder || (
                <div
                    className={classNames("value", moodClass, props.className)}>
                    {
                        ((props.change && value !== 0 && (value > 0 ? "+" : "-")) || "") +
                        Math.abs(value) + (props.percent ? "%" : "")
                    }
                </div>
            ) }
            <div className="label">{props.label}</div>
        </div>
    );
};

Badge.propTypes = {
    className: PropTypes.string,
    mood: PropTypes.number,
    label: PropTypes.string.isRequired,
    change: PropTypes.bool,
    formatter: PropTypes.func,
    percent: PropTypes.bool,
    value: PropTypes.number
};

Badge.defaultProps = {
    className: "",
    mood: undefined,
    change: false,
    formatter: v => v, /* identity function */
    percent: false,
    value: null
};

/**
 * Collection of badges used to display a quick summary of overall stats.
 */
class Badges extends Component {

    static propTypes = {
        /* Display badges inline with text. */
        inline: PropTypes.bool,

        /* 4 possible stats are displayed (all numeric). */
        average: PropTypes.number,
        responses: PropTypes.number,
        to_all_time: PropTypes.number,
        to_industry: PropTypes.number,

        filter: PropTypes.string.isRequired,
        industry: PropTypes.bool
    };

    static defaultProps = {
        inline: false,
        industry: true,
        average: null,
        responses: null,
        to_all_time: null,
        to_industry: null
    };

    /**
     * @constructor
     */
    constructor() {
        super();
    }

    /**
     * Rounds value to 1 decimal place.
     * @param   {number} x Unformatted percentage.
     * @returns {number}
     */
    static fmtPercent(x) {
        return +x.toFixed(1);
    }

    /**
     * Rounds value to 1 decimal place and abbreviates >thousand with a "k" suffix.
     * @param   {number} r Unformatted number of responses.
     * @returns {number}
     */
    static fmtResponses(r) {
        const length = r.toString().length;

        if (length <= 4) {
            return r;
        } else {
            return (+(r / 1000).toFixed(1)) + "k";
        }
    }

    /**
     * @override
     */
    render() {
        return (
            <div className={classNames("badges", { inline: this.props.inline })}>
                <Badge
                    value={this.props.average}
                    label={"Average"}
                    change={false}
                    percent={true}
                    formatter={Badges.fmtPercent}
                />
                <Badge
                    value={this.props.responses}
                    label={"Responses"}
                    change={false}
                    percent={false}
                    mood={100}
                    className="responses"
                    formatter={Badges.fmtResponses}
                />
                { this.props.filter !== "ALL_TIME" && (
                    <Badge
                        value={this.props.to_all_time}
                        label={"To All Time"}
                        change={true}
                        percent={true}
                        formatter={Badges.fmtPercent}
                    />
                ) }
                { this.props.industry && (
                    <Badge
                        value={this.props.to_industry}
                        label={"To Industry"}
                        change={true}
                        percent={true}
                        formatter={Badges.fmtPercent}
                    />
                ) }
            </div>
        );
    }
}

export { Badges };
