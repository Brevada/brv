import React, { Component } from "react";
import PropTypes from "prop-types";

/**
 * Loader animation which cycles through an array of messages.
 */
export default class Loader extends Component {

    static propTypes = {
        /* Messages to cycle through. */
        messages: PropTypes.arrayOf(PropTypes.string),
        className: PropTypes.string
    }

    static defaultProps = {
        messages: [],
        className: ""
    };

    /**
     * @constructor
     * @param  {object} props React props
     */
    constructor(props) {
        super(props);

        this.state = {
            /* The current message to display. */
            message: ""
        };

        /* Internal collection of messages which are rotated through. */
        this.messages = props.messages || [];

        this.cycleMessage = ::this.cycleMessage;
    }

    /**
     * @override
     */
    componentDidMount() {
        /* Display the first message. */
        this.cycleMessage();
    }

    /**
     * Shift all messages over one.
     * @returns {void}
     */
    cycleMessage() {
        this.setState({
            message: this.messages[0]
        }, () => {
            this.messages.push(this.messages.shift());

            /* Time between messages: random value in interval 2 - 4 seconds. */
            this.tmr = setTimeout(this.cycleMessage, Math.round(2000 + (Math.random() * 2000)));
        });
    }

    /**
     * @override
     */
    componentWillUnmount() {
        clearTimeout(this.tmr);
    }

    /**
     * @override
     */
    render() {
        return (
            <div className={this.props.className}>
                <div className="loader">
                    <div className="spinner">
                        <div className="bounce1"></div>
                        <div className="bounce2"></div>
                        <div className="bounce3"></div>
                    </div>
                    <div className="tip">
                        <span>{this.state.message || "Loading..."}</span>
                    </div>
                </div>
            </div>
        );
    }

}
