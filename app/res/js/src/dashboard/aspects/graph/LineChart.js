import React from "react";
import PropTypes from "prop-types";

import { ResponsiveContainer,
         LineChart as RechartsLineChart,
         Tooltip, Line, XAxis, YAxis, Text } from "recharts";

import AspectGraphTooltip from "dashboard/aspects/graph/AspectGraphTooltip";

/**
 * Custom recharts compat. x-axis for individual aspect graph.
 *
 * @param   {object} props React props
 * @param   {float} props.x x coordinate of tick
 * @param   {float} props.y y coordinate of tick
 * @param   {string[]} props.dates X-axis date labels.
 * @param   {object} props.payload Recharts data point.
 * @returns {JSX}
 */
const AspectXAxisTick = props => (
    <g transform={`translate(${props.x}, ${props.y})`}>
        <Text x={0} y={0} dy={+15} textAnchor="middle" className="x-axis-text">
            {props.dates[parseInt(props.payload.value)]}
        </Text>
    </g>
);

AspectXAxisTick.propTypes = {
    dates: PropTypes.arrayOf(PropTypes.string),
    payload: PropTypes.object,
    x: PropTypes.number,
    y: PropTypes.number
};

AspectXAxisTick.defaultProps = {
    dates: [],
    payload: {},
    x: null,
    y: null
};

/**
 * A single data point on the graph.
 * @param   {object} props Mainly inherited from recharts graph.
 * @returns {JSX}
 */
const Dot = props => { // eslint-disable-line complexity
    const {cx, cy, payload} = props;

    const style = {
        r: props.active ? 6 : 5.5,
        stroke: payload.empty ? "#9cddfe" : "#1bb0ff",
        strokeWidth: payload.empty ? 2 : 1,
        fill: payload.empty ? "#fff" : "#1bb0ff",
        cx,
        cy
    };

    return (<circle {...style} />);
};

Dot.propTypes = {
    active: PropTypes.bool,
    payload: PropTypes.object,
    cx: PropTypes.number,
    cy: PropTypes.number
};

Dot.defaultProps = {
    active: false,
    payload: {},
    cx: null,
    cy: null
};

/**
 * Aspect graph line chart view.
 *
 * @param   {object} props React props
 * @param   {object[]} props.data Data points
 * @param   {string[]} props.dates X-axis date labels
 * @param   {number[]} props.timestamps Timestamps
 * @returns {JSX}
 */
const LineChart = props => {
    return (
        <ResponsiveContainer>
            <RechartsLineChart
                data={props.data}
                margin={{ top: 5, right: 30, left: 30, botom: 5 }}>
                <Line
                    type="monotone"
                    dataKey={"value"}
                    stroke="#1bb0ff"
                    unit="%"
                    dot={<Dot />}
                    activeDot={<Dot active={true} />}
                    connectNulls={true}
                    animationDuration={350}
                />
                <YAxis
                    dataKey={"value"}
                    domain={["auto", "auto"]}
                    padding={{ bottom: 15, top: 15 }}
                    tick={false}
                    tickLine={false}
                    axisLine={false}
                    hide={true}
                />
                <XAxis
                    dataKey={"label"}
                    axisLine={true}
                    tickLine={false}
                    padding={{ top: 10 }}
                    tick={(
                        <AspectXAxisTick
                            dates={props.dates}
                            timestamps={props.timestamps}
                        />
                    )}
                />
                <Tooltip
                    content={(
                        <AspectGraphTooltip
                            dates={props.dates}
                            timestamps={props.timestamps}
                        />
                    )}
                    cursor={false}
                />
            </RechartsLineChart>
        </ResponsiveContainer>
    );
};

LineChart.propTypes = {
    dates: PropTypes.arrayOf(PropTypes.string).isRequired,
    timestamps: PropTypes.arrayOf(PropTypes.number).isRequired,
    data: PropTypes.arrayOf(PropTypes.object).isRequired
};

export { LineChart };
