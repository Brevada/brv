/* global brv */

import React, { Component } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

import HeaderButton from "feedback/HeaderButton";

/**
 * Header action constants (enum).
 * @type {Object}
 */
const HeaderActions = {
    COMMENT: "COMMENT",
    SUBMIT_COMMENT: "SUBMIT_COMMENT",
    SUBMIT_EMAIL: "SUBMIT_EMAIL",
    FINISH: "FINISH",
    CLOSE_DIALOG: "CLOSE_DIALOG"
};

/**
 * Header controls for main view. Contains comment and finish buttons.
 * @param   {object} props React props
 * @param   {boolean} props.enableComments Display option to comment.
 * @param   {boolean} props.enableSubmit Allow user to submit form (of dialog).
 * @param   {function} props.onAction Handles button click event, or any other action.
 * @returns {JSX}
 */
const FeedbackControls = props => {
    // eslint-disable-next-line require-jsdoc
    const onCommentClicked = () => (props.onAction(HeaderActions.COMMENT));
    // eslint-disable-next-line require-jsdoc
    const onFinishClicked = () => (props.onAction(HeaderActions.FINISH));

    const commentBtn = props.enableComments && (
        <HeaderButton
            label="comment"
            icon="fa-commenting-o"
            onClick={onCommentClicked}
        />
    );

    return (
        <div
            className={classNames("controls", {
                "single": !props.enableComments
            })}>
            {commentBtn}
            <HeaderButton
                label="finish"
                icon="fa-check-circle-o"
                onClick={onFinishClicked}
                disabled={!props.enableSubmit}
            />
        </div>
    );
};

FeedbackControls.propTypes = {
    enableComments: PropTypes.bool,
    enableSubmit: PropTypes.bool,
    onAction: PropTypes.func.isRequired
};

FeedbackControls.defaultProps = {
    enableComments: false,
    enableSubmit: true
};

/**
 * Header controls for comment dialog.
 * @param   {object} props React props
 * @param   {function} props.onAction Handles button click event or other dialog action.
 * @param   {boolean} props.enableSubmit Allow user to submit form (or dialog).
 * @returns {JSX}
 */
const CommentControls = props => {
    const lastForm = brv.feedback.session.getRemainingCount() === 0;

    // eslint-disable-next-line require-jsdoc
    const onCancel = () => (props.onAction(HeaderActions.CLOSE_DIALOG));
    // eslint-disable-next-line require-jsdoc
    const onSubmit = () => (props.onAction(HeaderActions.SUBMIT_COMMENT));

    return (
        <div className="controls">
            <HeaderButton
                label={lastForm ? "skip" : "cancel"}
                icon="fa-times-circle"
                negative={true}
                onClick={onCancel}
            />
            <HeaderButton
                label="submit comment"
                icon="fa-check-circle-o"
                onClick={onSubmit}
                disabled={!props.enableSubmit}
            />
        </div>
    );
};

CommentControls.propTypes = {
    enableSubmit: PropTypes.bool,
    onAction: PropTypes.func.isRequired
};

CommentControls.defaultProps = {
    enableSubmit: true
};

/**
 * Header controls for email dialog.
 * @param   {object} props React props
 * @param   {function} props.onAction Handles button click event or other dialog action.
 * @param   {boolean} props.enableSubmit Allow user to submit form (or dialog).
 * @returns {JSX}
 */
const EmailControls = props => {
    // eslint-disable-next-line require-jsdoc
    const onCancel = () => (props.onAction(HeaderActions.CLOSE_DIALOG));
    // eslint-disable-next-line require-jsdoc
    const onSubmit = () => (props.onAction(HeaderActions.SUBMIT_EMAIL));

    return (
        <div className="controls">
            <HeaderButton
                label="skip"
                icon="fa-times-circle"
                negative={true}
                onClick={onCancel}
            />
            <HeaderButton
                label="submit email"
                icon="fa-check-circle-o"
                onClick={onSubmit}
                disabled={!props.enableSubmit}
            />
        </div>
    );
};

EmailControls.propTypes = {
    enableSubmit: PropTypes.bool,
    onAction: PropTypes.func.isRequired
};

EmailControls.defaultProps = {
    enableSubmit: true
};

/**
 * Minified & simplistic header.
 * @param   {object} props React props
 * @param   {string} props.name Store name
 * @returns {JSX}
 */
const MinyHeader = props => (
    <div className="feedback-header miny-header">
        <div className="content">
            <div className="brand logo-lq"></div>
            <div className="heading">
                {"Give "}<span>{props.name}</span>{" Feedback"}
            </div>
        </div>
    </div>
);

MinyHeader.propTypes = {
    name: PropTypes.string.isRequired
};

/**
 * Feedback header.
 */
class Header extends Component {

    static propTypes = {
        name: PropTypes.string.isRequired,
        onComment: PropTypes.func,
        onFinish: PropTypes.func,
        showDialog: PropTypes.string,
        enableComments: PropTypes.bool,
        onAction: PropTypes.func,
        enableSubmit: PropTypes.bool
    };

    static defaultProps = {
        onComment: () => { /* no op */ },
        onFinish: () => { /* no op */ },
        onAction: () => { /* no op */ },
        enableComments: false,
        enableSubmit: false,
        showDialog: null
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {     };
        this.getControls = ::this.getControls;
    }

    /**
     * Returns the controls for the current environment.
     * @returns {JSX}
     */
    getControls() {
        const props = {
            onAction: this.props.onAction,
            enableSubmit: this.props.enableSubmit
        };

        if (!this.props.showDialog) {
            return (
                <FeedbackControls
                    {...props}
                    enableComments={this.props.enableComments}
                />
            );
        }

        const dialog = ({
            "COMMENT": (<CommentControls {...props} />),
            "EMAIL": (<EmailControls {...props} />)
        })[this.props.showDialog];

        return dialog || null;
    }

    /**
     * @override
     */
    render() {
        return (
            <div className="feedback-header">
                <div className="content">
                    <div className="brand logo-lq"></div>
                    <div className="heading">
                        {"Give "}<span>{this.props.name}</span>{" Feedback"}
                    </div>
                    { this.getControls() }
                </div>
            </div>
        );
    }
}

export { Header as default, MinyHeader, HeaderActions };
