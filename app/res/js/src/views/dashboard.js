require('expose?$!expose?jQuery!jquery');

import React from 'react';
import ReactDOM from 'react-dom';

import Dashboard from '../dashboard/Dashboard';

$(function() {
    ReactDOM.render(
        <Dashboard />,
        document.getElementById('dashboard-root')
    );
});
