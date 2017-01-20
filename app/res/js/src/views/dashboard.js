import React from 'react';
import ReactDOM from 'react-dom';
import docReady from 'doc-ready';

import DataLayer from 'forms/DataLayer';
import Loader from 'dashboard/Loader';

import Dashboard from 'dashboard/Dashboard';

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

docReady(function() {
    ReactDOM.render(
        (<DataLayer action="/api/store">
            <DashboardLinked />
        </DataLayer>),
        document.getElementById('dashboard-root')
    );
});
