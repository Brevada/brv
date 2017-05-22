import React, { Component } from "react";
import PropTypes from "prop-types";
import Dialog from "feedback/Dialog";

/**
 * Inactivity dialog.
 */
export default class Inactivity extends Component {

    static propTypes = {
        onClick: PropTypes.func
    };

    static defaultProps = {
        onClick: () => { /* no op */ }
    };

    /**
     * @constructor
     */
    constructor() {
        super();
    }

    /**
     * @override
     */
    render() {
        return (
            <Dialog
                className="dialog-inactivity"
                onOverlayClick={this.props.onClick}
                onContentClick={this.props.onClick}>
                <div className="message">
                    <span>{"If you're not done giving feedback, tap here."}</span>
                    <i className="fa fa-clock-o"></i>
                </div>
            </Dialog>
        );
    }
}
