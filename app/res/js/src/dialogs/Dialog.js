import React, { Component } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * Dialog Button Actions enums.
 * @type {string}
 */
export const DialogButtonActions = Object.freeze({
    CLOSE: "CLOSE",
    OPEN: "OPEN",
    SUCCESS: "SUCCESS"
});

/**
 * Individual dialog button.
 * @param   {object} props React props
 * @param   {string} props.action The dialog button action to invoke.
 * @returns {JSX}
 */
export const DialogButton = props => {
    const onClick = () => ( // eslint-disable-line require-jsdoc
        props.onClick(props.action)
    );

    return (
        <span
            className={"dialog-button"}
            onClick={onClick}>
            {props.label}
        </span>
    );
};

DialogButton.propTypes = {
    onClick: PropTypes.func.isRequired,
    label: PropTypes.string.isRequired,
    action: PropTypes.string.isRequired
};

/**
 * Dialog box which overlays onto the screen, blocking input from the
 * rest of the application until dismissed.
 */
export default class Dialog extends Component {

    static propTypes = {
        escapable: PropTypes.bool,
        onOverlayClick: PropTypes.func,
        onAction: PropTypes.func.isRequired,
        onContentClick: PropTypes.func,
        children: PropTypes.node.isRequired
    };

    static defaultProps = {
        escapable: false,
        onOverlayClick: () => { /* no op */ },
        onContentClick: () => { /* no op */ }
    };

    /**
     * @constructor
     * @param  {object} props React props
     */
    constructor(props) {
        super(props);

        this.onOverlayClick = ::this.onOverlayClick;
        this.onContentClick = ::this.onContentClick;
    }

    /**
     * Event handler when the "greyed out" area is clicked.
     * @returns {void}
     */
    onOverlayClick() {
        if (this.props.escapable) {
            this.props.onAction(DialogButtonActions.CLOSE);
        } else if (this.props.onOverlayClick) {
            this.props.onOverlayClick();
        }
    }

    /**
     * Event handler when content area is clicked.
     * @returns {void}
     */
    onContentClick() {
        if (this.props.onContentClick) {
            this.props.onContentClick();
        }
    }

    /**
     * @override
     */
    render() {
        return (
            <div className="dialog">
                <div
                    className={classNames("overlay", {
                        "escapable": this.props.escapable
                    })}
                    title={this.props.escapable ? "Click To Close" : ""}
                    onClick={this.onOverlayClick}></div>
                <div
                    className="content"
                    onClick={this.onContentClick}>
                    {React.Children.map(this.props.children, child => {
                        if (child.type === DialogButton) {
                            return React.cloneElement(child, {
                                onClick: this.props.onAction
                            });
                        }

                        return child;
                    })}
                </div>
            </div>
        );
    }
}
