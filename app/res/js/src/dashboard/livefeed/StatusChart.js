import React from 'react';
import ReactDOM from 'react-dom';

import { ResponsiveContainer, PieChart, Tooltip, Pie, XAxis, YAxis, Cell } from 'recharts';
import { Mood, MoodColor } from 'utils/Mood';

const StatusGraphTooltip = props => (
    <div className='graph-tooltip'>
        {props.payload[0].value}
    </div>
);

export default class StatusChart extends React.Component {

    constructor() {
        super();

        this.state = {
            data: [
                { name: 'Bad', value: 30 },
                { name: 'Poor', value: 70 },
                { name: 'Neutral', value: 45 },
                { name: 'Good', value: 78 },
                { name: 'Amazing', value: 120 }
            ]
        };
    }

    render() {
        return (
            <div className='status-chart'>
                <ResponsiveContainer>
                    <PieChart>
                        <Pie data={this.state.data} innerRadius={20} outerRadius={60} fill="#82ca9d">
                            {this.state.data.map((entry, index) => (
                                <Cell key={index} fill={MoodColor(Mood(100/index))} />
                            ))}
                        </Pie>
                        <Tooltip content={<StatusGraphTooltip />} cursor={false} />
                    </PieChart>
                </ResponsiveContainer>
            </div>
        );
    }

}
