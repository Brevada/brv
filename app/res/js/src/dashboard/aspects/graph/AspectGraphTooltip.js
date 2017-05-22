import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

import moment from "moment";

/**
 * Custom recharts compat. tooltip for individual aspect graph.
 *
 * @param   {object} props React props
 * @param   {boolean} props.active Data point triggering tooltip is active.
 * @param   {object[]} props.payload Recharts data payload.
 * @param   {integer[]} props.timestamps Array of timestamps.
 * @returns {JSX}
 */
const AspectGraphTooltip = props => {
    if (!props.active) return null;

    const tsIndex = parseInt(props.payload[0].payload.label);

    return (
        <div
            className={classNames("graph-tooltip", {
                "empty": props.payload[0].payload.empty
            })}>
            <span className="value">
                {props.payload[0].payload.empty ?
                    <span className="empty">{"No Responses"}</span>
                 : props.payload[0].value + props.payload[0].unit}
            </span>
            <span className="specific">
                {moment.unix(props.timestamps[tsIndex]).format("D/MM/YYYY")}
            </span>
        </div>
    );
};

AspectGraphTooltip.propTypes = {
    active: PropTypes.bool,
    payload: PropTypes.arrayOf(PropTypes.object),
    timestamps: PropTypes.arrayOf(PropTypes.number)
};

AspectGraphTooltip.defaultProps = {
    active: false,
    payload: [],
    timestamps: []
};

export default AspectGraphTooltip;
