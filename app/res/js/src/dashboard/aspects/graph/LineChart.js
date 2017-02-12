import React from 'react';

import moment from 'moment';
import { ResponsiveContainer, LineChart as RechartsLineChart, Tooltip, Line, XAxis, YAxis } from 'recharts';

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
            <div className='graph-tooltip'>
                <span className='value'>
                    {props.payload[0].value + props.payload[0].unit}
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
 * Aspect graph line chart view.
 *
 * @param {object} props
 */
const LineChart = props => (
    <ResponsiveContainer>
        <RechartsLineChart
            data={props.data}
            margin={{ top: 5, right: 30, left: 20, botom: 5 }}
        >
            <Line
                type="monotone"
                dataKey={'value'}
                stroke="#1bb0ff"
                unit="%"
                dot={{ stroke: '#1bb0ff', strokeWidth: 1, r: 5.5, fill: '#1bb0ff' }}
                activeDot={{ stroke: '#1bb0ff', strokeWidth: 1, r: 6, fill: '#1bb0ff' }}
            />
            <YAxis dataKey={'value'} domain={['auto', 'auto']} padding={{ bottom: 15, top: 15 }} hide={true} />
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

export { LineChart };
