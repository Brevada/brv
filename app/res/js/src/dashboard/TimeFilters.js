import React from "react";
import PropTypes from "prop-types";

import { NavigationButton } from "components/NavigationButton";
import { HyperLink } from "components/HyperLink";
import { Filter } from "dashboard/aspects/Filter";

/**
 * Time Interval Toolbar
 *
 * @param   {object} props React props
 * @param   {object[]} props.options Filter options.
 * @returns {JSX}
 */
const TimeFilters = props => (
    <div className="time-filter">
        {props.options.map(o => (
            <NavigationButton
                key={o.view}
                label={o.label || Filter.toLabel(o.view)}
                value={o.view}
                onClick={props.onChange}
                active={props.filter === o.view}
            />
        ))}
        {props.actionLabel && (
            <HyperLink
                label={props.actionLabel}
                onClick={props.onAction}
            />
        )}
    </div>
);

TimeFilters.propTypes = {
    options: PropTypes.arrayOf(PropTypes.object),
    filter: PropTypes.string.isRequired,
    actionLabel: PropTypes.string,
    onAction: PropTypes.func
};

TimeFilters.defaultProps = {
    options: [],
    actionLabel: null,
    onAction: () => { /* no op */ }
};

export default TimeFilters;
