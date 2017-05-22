import React from "react";

import CustomerUpdates from "dashboard/livefeed/CustomerUpdates";

/**
 * Live Feed
 */
export default class LiveFeed extends React.Component {

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {};
    }

    /**
     * @override
     */
    render() { // eslint-disable-line class-methods-use-this
        return (
            <div className="livefeed">
                <CustomerUpdates />
            </div>
        );
    }

}
