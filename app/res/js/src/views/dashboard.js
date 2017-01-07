require('expose?$!expose?jQuery!jquery');

import React from 'react';
import ReactDOM from 'react-dom';

import DataLayer from 'forms/DataLayer';
import Dashboard from 'dashboard/Dashboard';
import Loader from 'dashboard/Loader';

const DashboardLinked = props => {
    if (!props.data.id || props.loading) {
        return (
            <Loader
                className='view'
                messages={[
                    "Preparing your dashboard...",
                    "Retrieving store information...",
                    "Downloading analytics...",
                    "Analyzing data...",
                    "Crunching numbers..."
                ]}
            />
        );
    } else {
        return (<Dashboard {...props} />);
    }
};

$(function() {
    ReactDOM.render(
        (<DataLayer action="/api/store">
            <DashboardLinked />
        </DataLayer>),
        document.getElementById('dashboard-root')
    );
});
