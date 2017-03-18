import React from 'react';
import classNames from 'classnames';

import moment from 'moment';
import { ResponsiveContainer,
         LineChart as RechartsLineChart,
         Tooltip, Line, XAxis, YAxis } from 'recharts';

/**
 * Custom recharts compat. tooltip for individual aspect graph.
 *
 * @param {object} props
 * @param {boolean} props.active Data point triggering tooltip is active.
 * @param {object[]} props.payload Recharts data payload.
 * @param {integer[]} props.timestamps Array of timestamps.
 */
const AspectGraphTooltip = props => {
    if (props.active) {
        const tsIndex = parseInt(props.payload[0].payload.label);
        return (
            <div className={classNames('graph-tooltip', {
                'empty': props.payload[0].payload.empty
            })}>
                <span className='value'>
                    {props.payload[0].payload.empty ? (
                        <span className='empty'>No Responses</span>
                    ) : props.payload[0].value + props.payload[0].unit}
                </span>
                <span className='specific'>
                    {moment.unix(props.timestamps[tsIndex]).format('D/MM/YYYY')}
                </span>
            </div>
        );
    }

    return null;
};

/**
 * Custom recharts compat. x-axis for individual aspect graph.
 *
 * @param {object} props
 * @param {float} props.x
 * @param {float} props.y
 * @param {string[]} props.dates X-axis date labels.
 */
const AspectXAxisTick = props => (
    <g transform={`translate(${props.x}, ${props.y})`}>
        <text x={0} y={0} dy={+15} textAnchor='middle' className='x-axis-text'>
            {props.dates[parseInt(props.payload.value)]}
        </text>
    </g>
);

/**
 * A single data point on the graph.
 * @param {object} props Mainly inherited from recharts graph.
 */
const Dot = props => {
    const {cx, cy, payload} = props;

    const style = {
        r: props.active ? 6 : 5.5,
        stroke: payload.empty ? "#9cddfe" : "#1bb0ff",
        strokeWidth: payload.empty ? 2 : 1,
        fill: payload.empty ? "#fff" : "#1bb0ff",
        cx: cx,
        cy: cy
    };

    return (<circle {...style} />);
};

/**
 * Aspect graph line chart view.
 *
 * @param {object} props
 */
const LineChart = props => {
    return (
        <ResponsiveContainer>
            <RechartsLineChart
                data={props.data}
                margin={{ top: 5, right: 30, left: 30, botom: 5 }}>
                <Line
                    type="monotone"
                    dataKey={'value'}
                    stroke="#1bb0ff"
                    unit="%"
                    dot={<Dot />}
                    activeDot={<Dot active={true} />}
                    connectNulls={true}
                    animationDuration={350}
                />
                <YAxis
                    dataKey={'value'}
                    domain={['auto', 'auto']}
                    padding={{ bottom: 15, top: 15 }}
                    hide={true}
                />
                <XAxis
                    dataKey={'label'}
                    axisLine={true}
                    tickLine={false}
                    padding={{ top: 10 }}
                    tick={<AspectXAxisTick dates={props.dates} timestamps={props.timestamps} />}
                />
                <Tooltip
                    content={<AspectGraphTooltip dates={props.dates} timestamps={props.timestamps} />}
                    cursor={false}
                />
            </RechartsLineChart>
        </ResponsiveContainer>
    );
};

export { LineChart };
