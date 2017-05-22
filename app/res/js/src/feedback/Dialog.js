/* global brv */

import React, { Component } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * Dialog
 */
export default class Dialog extends Component {

    static propTypes = {
        onAttention: PropTypes.func,
        className: PropTypes.string,
        children: PropTypes.node.isRequired
    };

    static defaultProps = {
        onAttention: () => { /* no op */ },
        className: ""
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.onAttention = ::this.onAttention;
    }

    /**
     * @override
     */
    componentDidMount() { // eslint-disable-line class-methods-use-this
        window.brv && brv.feedback && brv.feedback.scroll.lock();
    }

    /**
     * @override
     */
    componentWillUnmount() { // eslint-disable-line class-methods-use-this
        window.brv && brv.feedback && brv.feedback.scroll.lock(false);
    }

    /**
     * Handles dialog onClick/onFocus event. We'll use this event to redirect
     * input focus.
     * @returns {void}
     */
    onAttention() {
        if (this.props.onAttention) this.props.onAttention();
    }

    /**
     * @constructor
     */
    render() {
        return (
            <div
                className={classNames("dialog-overlay", this.props.className)}
                onClick={this.onAttention}
                onFocus={this.onAttention}>
                <div className="dialog-content">
                    {this.props.children}
                </div>
            </div>
        );
    }
}
