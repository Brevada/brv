import React from 'react';
import ReactDOM from 'react-dom';

import { NavigationButton } from 'components/NavigationButton';
import { HyperLink } from 'components/HyperLink';
import { Filter } from 'dashboard/aspects/Filter';

/**
 * Time Interval Toolbar
 *
 * @param {object} props
 * @param {object[]} props.options
 */
const TimeFilters = props => (
    <div className='time-filter'>
        {props.options.map(o => (
            <NavigationButton
                key={o.view}
                label={o.label || Filter.toLabel(o.view)}
                value={o.view}
                onClick={props.onChange}
                active={props.filter === o.view}
            />
        ))}
        <HyperLink label={props.actionLabel} onClick={props.onAction} />
    </div>
);

export default TimeFilters;
