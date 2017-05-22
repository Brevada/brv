import React from "react";

/**
 * Customer Updates
 */
export default class CustomerUpdates extends React.Component {

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
            <div className="customer-updates">
                {"Customer 1"}
            </div>
        );
    }

}
