import React from 'react';
import ReactDOM from 'react-dom';

import CustomerUpdates from './livefeed/CustomerUpdates';
import StatusChart from './livefeed/StatusChart';

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
