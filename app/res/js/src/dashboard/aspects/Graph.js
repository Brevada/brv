import React from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';

import { DateCluster } from '../../utils/DateCluster';
import { ResponsiveContainer, LineChart, Tooltip, Line, XAxis, YAxis } from 'recharts';

const AspectGraphTooltip = props => (
    <div className='graph-tooltip'>
        <span className='value'>
            {props.payload[0].value + props.payload[0].unit}
        </span>
        <span className='specific'>
            {moment.unix(props.timestamps[parseInt(props.payload[0].payload.label)]).format('D/MM/YYYY')}
        </span>
    </div>
);

const AspectXAxisTick = props => (
    <g transform={`translate(${props.x}, ${props.y})`}>
        <text x={0} y={0} dy={+15} textAnchor='middle' className='x-axis-text'>
            {props.dates[parseInt(props.payload.value)]}
        </text>
    </g>
);

class Graph extends React.Component {
    constructor() {
        super();
        this.state = {
            data: []
        };

        /* Used as internal DS. Don't want to affect state. */
        this.timestamps = [];
        this.dates = [];

        this.updateData = this.updateData.bind(this);
    }

    shouldComponentUpdate(nextProps, nextState) {
        return nextState.data !== this.state.data;
    }

    componentWillReceiveProps(nextProps) {
        if (nextProps.data !== this.props.data) this.updateData();
    }

    componentDidMount() {
        this.updateData();
    }

    updateData() {
        this.timestamps = this.props.data.map(d => Math.floor((d.from+d.to)/2));
        this.dates = DateCluster.getLabels(this.timestamps, {
            day: d => d.format('ddd').substring(0,1)
        });

        this.setState({
            data: this.props.data.map((datum, index) => (
                {
                    value: datum.average === null ? null : +datum.average.toFixed(2),
                    label: index
                }
            ))
        });
    }

    render() {
        return (
            <div className='graph'>
                <ResponsiveContainer>
                    <LineChart
                        data={this.state.data}
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
                            tick={<AspectXAxisTick dates={this.dates} timestamps={this.timestamps} />}
                        />
                        <Tooltip
                            content={<AspectGraphTooltip dates={this.dates} timestamps={this.timestamps} />}
                            cursor={false}
                        />
                    </LineChart>
                </ResponsiveContainer>
            </div>
        );
    }
}

export { Graph };
