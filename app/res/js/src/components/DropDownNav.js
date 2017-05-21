import React, { Component } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * A single option in the DropDownButton.
 *
 * @param {object} props Properties.
 * @param {string} props.label The display text.
 * @param {boolean} props.active Indicates the option is currently selected.
 * @param {function(Event)} props.onClick Fired when the drop down option
 * is selected.
 *
 * @returns {JSX}
 */
const DropDownOption = props => (
    <div
        className={classNames("option", {
            active: Boolean(props.active)
        })}
        onClick={props.onClick}>
        {props.label}
    </div>
);

DropDownOption.propTypes = {
    active: PropTypes.bool,
    onClick: PropTypes.func.isRequired,
    label: PropTypes.string.isRequired
};

DropDownOption.defaultProps = {
    active: false
};

/**
 * A navigation button capable of displaying a dropdown of options.
 */
class DropDownButton extends Component {

    static propTypes = {
        label: PropTypes.string.isRequired,
        children: PropTypes.node.isRequired
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {
            /* Holds the dropdown menu's "open" or visible state. */
            open: false
        };

        this.toggle = ::this.toggle;
        this.close = ::this.close;
    }

    /**
     * Toggles the dropdown options' visiblity.
     * @returns {void}
     */
    toggle() {
        this.setState(s => ({ open: !s.open }));
    }

    /**
     * Closes the dropdown box.
     * @returns {void}
     */
    close() {
        this.setState({ open: false });
    }

    /**
     * @override
     */
    render() {
        return (
            <div
                className={classNames("dropdown-btn", {
                    "open": this.state.open
                })}
                onClick={this.toggle}
                onBlur={this.close}
                tabIndex={0}>
                <div className="label">
                    {this.props.label}
                    <i className="fa fa-chevron-down"></i>
                </div>
                <div className="options">{this.props.children}</div>
            </div>
        );
    }
}

export { DropDownButton, DropDownOption };
