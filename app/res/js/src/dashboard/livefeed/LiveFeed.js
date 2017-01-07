import React from 'react';
import ReactDOM from 'react-dom';

import CustomerUpdates from 'dashboard/livefeed/CustomerUpdates';
import StatusChart from 'dashboard/livefeed/StatusChart';

export default class LiveFeed extends React.Component {

    constructor() {
        super();

        this.state = {};
    }

    render() {
        return (
            <div className='livefeed'>
                <CustomerUpdates />
                <StatusChart />
            </div>
        );
    }

}
