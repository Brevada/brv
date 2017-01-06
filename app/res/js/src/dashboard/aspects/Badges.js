import React from 'react';
import ReactDOM from 'react-dom';

import { Mood } from '../../utils/Mood';

const Badge = props => {
    let moodClass = props.change ? Mood(props.value, -100) : Mood(props.value);
    moodClass = typeof props.mood === 'undefined' ? moodClass : Mood(props.mood);

    let value = props.formatter && props.value !== null ? props.formatter(props.value) : props.value;

    return (
        <div className={'badge ' + (props.className || '')}>
            { value === null && (
                <div className={'value loading ' + Mood(50) + ' ' + (props.className || '')}>
                    N/A
                </div>
            ) }
            { value !== null && (
                <div className={'value ' + moodClass + ' ' + (props.className || '')}>
                    {(props.change ?
                        (value > 0 ? '+' : (value == 0 ? '' : '-'))
                    : '') + Math.abs(value) + (props.percent ? '%' : '')}
                </div>
            ) }
            <div className='label'>{props.label}</div>
        </div>
    );
};

const Badges = props => (
    <div className='badges'>
        <Badge
            value={props.summary.average}
            label={'Average'}
            change={false}
            percent={true}
            formatter={x => +x.toFixed(1)}
        />
        <Badge
            value={props.summary.responses}
            label={'Responses'}
            change={false}
            percent={false}
            mood={100}
            className='responses'
            formatter={ r => {
                let length = r.toString().length;
                if (length <= 4) {
                    return r;
                } else {
                    return (+(r/1000).toFixed(1)) + 'k';
                }
            }}
        />
        { props.filter !== 'ALL_TIME' && (
            <Badge
                value={props.summary.to_all_time}
                label={'To All Time'}
                change={true}
                percent={true}
                formatter={x => +x.toFixed(2)}
            />
        ) }
        <Badge
            value={props.summary.to_industry}
            label={'To Industry'}
            change={true}
            percent={true}
            formatter={x => +x.toFixed(1)}
        />
    </div>
);

export { Badges };
