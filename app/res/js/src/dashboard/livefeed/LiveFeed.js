import React from 'react';
import ReactDOM from 'react-dom';

import CustomerUpdates from 'dashboard/livefeed/CustomerUpdates';

export default class LiveFeed extends React.Component {

    constructor() {
        super();

        this.state = {};
    }

    render() {
        return (
            <div className='livefeed'>
                <CustomerUpdates />
            </div>
        );
    }

}
