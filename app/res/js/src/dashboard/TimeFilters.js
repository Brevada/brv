import React from 'react';
import ReactDOM from 'react-dom';

import { NavigationButton } from '../components/NavigationButton';
import { Link } from '../components/Link';

const TimeFilters = props => (
    <div className='time-filter'>
        <NavigationButton
            label={'Today'}
            view={'TODAY'}
            onClick={props.onChangeFilter}
            active={props.filter === 'TODAY'}
        />
        <NavigationButton
            label={'Past Week'}
            view={'PAST_WEEK'}
            onClick={props.onChangeFilter}
            active={props.filter === 'PAST_WEEK'}
        />
        <NavigationButton
            label={'Past Month'}
            view={'PAST_MONTH'}
            onClick={props.onChangeFilter}
            active={props.filter === 'PAST_MONTH'}
        />
        <NavigationButton
            label={'All Time'}
            view={'ALL_TIME'}
            onClick={props.onChangeFilter}
            active={props.filter === 'ALL_TIME'}
        />
        <Link label={'+ Ask Something New'} onClick={props.onNewAspect} />
    </div>
);

export default TimeFilters;
