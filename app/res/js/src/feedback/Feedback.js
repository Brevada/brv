/* global brv */

import React, { Component } from "react";
import PropTypes from "prop-types";
import setStatePromise from "utils/StatePromise";

import Header, { MinyHeader, HeaderActions } from "feedback/Header";
import Aspects from "feedback/Aspects";
import CommentDialog from "feedback/dialogs/Comment";
import EmailDialog from "feedback/dialogs/Email";
import InactivityDialog from "feedback/dialogs/Inactivity";

const DURATION = Object.freeze({
    RESET: 10000,
    INACTIVITY: 20000,
    INACTIVITY_COMMENT: 25000,
    INACTIVITY_WARNING: 8000
});

const DIALOG_TYPE = Object.freeze({
    EMAIL: "EMAIL",
    COMMENT: "COMMENT",
    NONE: null
});

const EMAIL_DIALOG_ORDER = Object.freeze({
    ALWAYS_BEFORE: 3,
    ALWAYS_AFTER: 2,
    AFTER_IF_POOR: 1,
    NEVER: 0
});

/**
 * Message to display after a feedback session has ended.
 * @param   {object} props React props
 * @param   {string} props.name The store name
 * @param   {function} props.onReset Optional onReset event handler, triggered
 * when user clicks the reset button.
 * @returns {void}
 */
const FeedbackGiven = props => (
    <div className={"ly flex-v defined-size feedback-container dialog-none state-done"}>
        <MinyHeader name={props.name} />
        <div className="thanks">
            <span>{"Thank you for giving feedback!"}</span>
            { props.onReset &&
                <div
                    className="btn btn-refresh"
                    onClick={props.onReset}>
                    <i className={"fa fa-refresh"}></i>
                </div>
             }
        </div>
    </div>
);

FeedbackGiven.propTypes = {
    name: PropTypes.string.isRequired,
    onReset: PropTypes.func
};

FeedbackGiven.defaultProps = {
    onReset: null
};

/**
 * The feedback view.
 */
export default class Feedback extends Component {

    static propTypes = {
        storeId: PropTypes.number.isRequired,
        data: PropTypes.object
    };

    static defaultProps = {
        data: {}
    };

    static initialState = {

        /* Indicates whether at least one aspect has been rated. */
        feedbackGiven: false,

        /* Indicates that a comment is ready for submission. */
        pendingComment: false,

        /* Indicates a comment has been submitted. */
        commentGiven: false,

        /* Indicates whether an email has been submitted. */
        emailGiven: false,

        /* Determines which dialog to show if any. */
        showDialog: DIALOG_TYPE.NONE,

        /* Indicates user's session is complete. */
        done: false,

        /* Used to force entire vdom reset. */
        reset: 1,

        /* Unique session token to group customer responses. */
        session: "",

        /* Indicates visibility of inactivity warning */
        showInactivityWarning: false
    };

    /**
     * @constructor
     * @param   {object} props React props
     */
    constructor(props) {
        super(props);

        /* Contains reference to current/last dialog's form.
         * Not part of state, since it doesn't affect render and should
         * not trigger redraw. */
        this.dialogForm = null;

        this._tmrReset = undefined;
        this._tmrInactivity = undefined;

        this.state = Object.assign(
            {},
            Feedback.initialState,

            /* Default to EMAIL screen if config set. */
            props.data && props.data.template_location === 3 ?
            { showDialog: DIALOG_TYPE.EMAIL } :
            {},

            { session: (() => {
                brv.feedback && brv.feedback.session.init();

                return brv.feedback.session.getToken();
            })() }
        );
    }

    /**
     * @override
     */
    componentWillMount() { // eslint-disable-line max-statements
        /* Bind methods. */

        this.onAspectSubmitted = ::this.onAspectSubmitted;
        this.onHeaderAction = ::this.onHeaderAction;
        this.closeDialog = ::this.closeDialog;
        this.showDialogComment = ::this.showDialogComment;
        this.showDialogEmail = ::this.showDialogEmail;

        this.onFinish = ::this.onFinish;
        this.onCommentSubmit = ::this.onCommentSubmit;
        this.onEmailSubmit = ::this.onEmailSubmit;

        this.reset = ::this.reset;
        this.completeSession = ::this.completeSession;

        this.getDialog = ::this.getDialog;

        this.onInactive = ::this.onInactive;
        this.resetInactivity = ::this.resetInactivity;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Resets session.
     * @returns {void}
     */
    reset() {
        clearTimeout(this._tmrReset);
        brv.feedback && brv.feedback.session.init();

        if (this._unmounted) return;

        this.setState(s => Object.assign({}, Feedback.initialState, {
            reset: (s.reset + 1) % 100, /* Arbitrary cycle length. */
            session: brv.feedback.session.getToken()
        }));

        this.resetInactivity();
    }

    /**
     * Resets inactivity timer.
     * @returns {void}
     */
    resetInactivity() {
        clearTimeout(this._tmrInactivity);

        if (this._unmounted || !(window.brv && window.brv.env && window.brv.env.IS_DEVICE)) {
            return;
        }

        setStatePromise.call(this, state => {
            return state.showInactivityWarning ? {
                showInactivityWarning: false
            } : {};
        }).then(state => {
            /* If on comment screen, wait longer. */
            this._tmrInactivity = setTimeout(this.onInactive,
                state.showDialog === DIALOG_TYPE.COMMENT ?
                DURATION.INACTIVITY_COMMENT :
                DURATION.INACTIVITY
            );
        });
    }

    /**
     * Tests whether currently in non-session state.
     * We are in a non-session state if waiting for session to start or not active.
     * @returns {boolean}
     */
    isNonSession() {
        const nonSession = (!this.state.feedbackGiven && !(this.state.commentGiven ||
            this.state.pendingComment) && !this.state.emailGiven) || this.state.done;

        return nonSession;
    }

    /**
     * User is inactive.
     * @returns {void}
     */
    onInactive() {
        if (this._unmounted) return;

        if (this.state.showInactivityWarning) {
            /* Warning has already been shown. */
            this.reset();

            return;
        }

        /* Don't do anything if session hasn't "started". */
        if (this.isNonSession()) return;

        /* Set timeout for warning. */
        setStatePromise.call(this, {
            showInactivityWarning: true
        }).then(() => {
            clearTimeout(this._tmrInactivity);
            this._tmrInactivity = setTimeout(
                this.onInactive,
                DURATION.INACTIVITY_WARNING
            );
        });
    }

    /**
     * Completes the current session.
     * @returns {void}
     */
    completeSession() {
        clearTimeout(this._tmrReset);

        /* Complete session. */
        setStatePromise.call(this, {
            done: true
        }).then(() => {
            brv.feedback.session && brv.feedback.session.complete();

            /* If it's a device, reset screen after timeout. */
            if (window.brv && window.brv.env && window.brv.env.IS_DEVICE) {
                this._tmrReset = setTimeout(this.reset, DURATION.RESET);
            }

            this.resetInactivity();
        });
    }

    /**
     * Handler for when feedback has been submitted for an aspect.
     * Occurs after actual submission to storage/network.
     * @returns {void}
     */
    onAspectSubmitted() {
        this.resetInactivity();

        /* At least one aspect has been rated. */
        setStatePromise.call(this, {
            feedbackGiven: true
        }).then(() => {

            /* If a comment has been given and there are no more aspects, consider
             * this a finish event. */
            if (brv.feedback.session.getRemainingCount() === 0) {
                /* No aspects left. */
                if (!this.state.commentGiven && this.props.data.allow_comments) {
                    /* Prompt for comment. */
                    this.showDialogComment();
                    return;
                }

                this.onFinish();
            }
        });
    }

    /**
     * Closes all dialogs.
     * @returns {void}
     */
    closeDialog() {
        this.setState({
            showDialog: DIALOG_TYPE.NONE
        });
    }

    /**
     * Shows the comment dialog.
     * @returns {void}
     */
    showDialogComment() {
        this.setState({
            showDialog: DIALOG_TYPE.COMMENT
        });
    }

    /**
     * Shows the email dialog.
     * @returns {void}
     */
    showDialogEmail() {
        this.setState({
            showDialog: DIALOG_TYPE.EMAIL
        });
    }

    /**
     * Handles header button click event.
     * @param   {HeaderAction} action Header action
     * @returns {void}
     */
    onHeaderAction(action) {
        this.resetInactivity();

        const actionHandler = {
            [HeaderActions.COMMENT]: () => {
                this.showDialogComment();
            },

            [HeaderActions.SUBMIT_EMAIL]: () => {
                actionHandler[HeaderActions.SUBMIT_COMMENT]();
            },
            [HeaderActions.SUBMIT_COMMENT]: () => {
                this.dialogForm && this.dialogForm.submit();
            },

            [HeaderActions.FINISH]: () => {
                this.onFinish();
            },

            [HeaderActions.CLOSE_DIALOG]: () => {
                /* Email is shown at "end". */
                const emailAtEnd = this.props.data.template_location === EMAIL_DIALOG_ORDER.AFTER_IF_POOR ||
                        this.props.data.template_location === EMAIL_DIALOG_ORDER.ALWAYS_AFTER;

                if (this.state.showDialog === DIALOG_TYPE.COMMENT &&
                        brv.feedback.session.getRemainingCount() === 0) {

                        /* Closing comment but no aspects remaining.
                         * Consider this finished. */
                    this.onFinish();
                } else if (this.state.showDialog === DIALOG_TYPE.EMAIL && emailAtEnd) {
                    this.completeSession();
                }

                this.closeDialog();
            }
        };

        actionHandler[action]();
    }

    /**
     * On finish event.
     * @returns {void}
     */
    onFinish() {
        if ((this.props.data.template_location === EMAIL_DIALOG_ORDER.AFTER_IF_POOR &&
            brv.feedback.session.hasPoor()) ||
            this.props.data.template_location === EMAIL_DIALOG_ORDER.ALWAYS_AFTER) {
            this.showDialogEmail();
        } else {
            /* Do not show email dialog. Complete the session. */
            this.completeSession();
        }
    }

    /**
     * On email submitted event.
     * @returns {void}
     */
    onEmailSubmit() {
        setStatePromise.call(this, {
            emailGiven: true
        }).then(() => {
            if (this.props.data.template_location === EMAIL_DIALOG_ORDER.ALWAYS_BEFORE) {
                this.closeDialog();
            } else {
                this.completeSession();
            }
        });
    }

    /**
     * On comment submitted event.
     * @returns {void}
     */
    onCommentSubmit() {
        setStatePromise.call(this, {
            commentGiven: true
        }).then(() => {
            if (brv.feedback.session.getRemainingCount() === 0) {
                this.onFinish();

                return;
            }

            this.closeDialog();
        });
    }

    /**
     * Gets the dialog according to the current environment (mainly
     * showDialog state).
     * @returns {JSX}
     */
    getDialog() {
        if (!this.state.showDialog) return null;

        /* eslint-disable react/jsx-no-bind */
        const dialogMap = {
            [DIALOG_TYPE.COMMENT]: (
                <CommentDialog
                    form={f => {
                        this.dialogForm = f;
                    }}
                    session={this.state.session}
                    message={this.props.data.comment_message}
                    onValid={() => this.setState({ pendingComment: true })}
                    onInvalid={() => this.setState({ pendingComment: false })}
                    onSubmit={this.onCommentSubmit}
                />
            ),

            [DIALOG_TYPE.EMAIL]: (
                <EmailDialog
                    form={f => {
                        this.dialogForm = f;
                    }}
                    session={this.state.session}
                    onSubmit={this.onEmailSubmit}
                />
            )
        };

        /* eslint-enable react/jsx-no-bind */

        return dialogMap[this.state.showDialog];
    }

    /**
     * @override
     */
    render() { // eslint-disable-line complexity
        const dialogClass = (this.state.showDialog || "none").toLowerCase();
        const isDevice = window.brv && window.brv.env && window.brv.env.IS_DEVICE;

        if (this.state.done) {
            return (
                <FeedbackGiven
                    name={this.props.data.name}
                    {...( (isDevice && { onReset: this.reset }) || {} )}
                />
            );
        }

        return (
            <div
                key={this.state.reset}
                className={`ly flex-v defined-size feedback-container dialog-${dialogClass}`}
                onClick={this.resetInactivity}
                onMouseMove={this.resetInactivity}>

                { this.state.showInactivityWarning &&
                    <InactivityDialog onClick={this.resetInactivity} />
                 }

                <Header
                    name={this.props.data.name}
                    onAction={this.onHeaderAction}
                    showDialog={this.state.showDialog}
                    enableComments={this.props.data.allow_comments && !this.state.commentGiven}
                    enableSubmit={
                        (this.state.showDialog == DIALOG_TYPE.COMMENT && this.state.pendingComment) ||
                        this.state.showDialog == DIALOG_TYPE.EMAIL ||
                        (!this.state.showDialog && (this.state.feedbackGiven || this.state.commentGiven))
                    }
                />
                <div className="scrollable">
                    { this.getDialog() }
                    <Aspects
                        aspects={this.props.data.aspects}
                        onSubmit={this.onAspectSubmitted}
                        session={this.state.session}
                    />
                </div>
            </div>
        );
    }

}
