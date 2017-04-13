import React from 'react';

import Header, { MinyHeader, HeaderActions } from 'feedback/Header';
import Aspects from 'feedback/Aspects';
import CommentDialog from 'feedback/dialogs/Comment';
import EmailDialog from 'feedback/dialogs/Email';

/**
 * The feedback view.
 */
export default class Feedback extends React.Component {

    static propTypes = {
        storeId: React.PropTypes.number.isRequired
    };

    constructor(props) {
        super(props);

        this.RESET_DURATION = 10000;

        this._initialState = {
            /* Indicates whether at least one aspect has been rated. */
            feedbackGiven: false,

            /* Indicates that a comment is ready for submission. */
            pendingComment: false,
            /* Indicates a comment has been submitted. */
            commentGiven: false,

            /* Determines which dialog to show if any. */
            showDialog: false,

            /* Indicates user's session is complete. */
            done: false,

            /* Used to force entire vdom reset. */
            reset: 1,

            /* Unique session token to group customer responses. */
            session: ''
        };

        /* Default to EMAIL screen if config set. */
        if (props.data && props.data.template_location === 3) {
            this._initialState.showDialog = 'EMAIL';
        }

        this.state = this._initialState;

        /* Contains reference to current/last dialog's form.
         * Not part of state, since it doesn't affect render and should
         * not trigger redraw. */
        this.dialogForm = null;

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

        this._tmrReset = undefined;

        brv.feedback && brv.feedback.session.init();
        this.state.session = brv.feedback.session.getToken();
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Resets session.
     */
    reset() {
        clearTimeout(this._tmrReset);
        brv.feedback && brv.feedback.session.init();
        if (this._unmounted) return;
        this.setState(s => (Object.assign({}, this._initialState, {
            reset: (s.reset+1) % 100, /* Arbitrary cycle length. */
            session: brv.feedback.session.getToken()
        })));
    }

    /**
     * Completes the current session.
     */
    completeSession() {
        clearTimeout(this._tmrReset);

        /* Complete session. */
        this.setState({
            done: true
        }, () => {
            brv.feedback.session && brv.feedback.session.complete();
            this._tmrReset = setTimeout(this.reset, this.RESET_DURATION);
        });
    }

    /**
     * Handler for when feedback has been submitted for an aspect.
     * Occurs after actual submission to storage/network.
     */
    onAspectSubmitted() {
        /* At least one aspect has been rated. */
        this.setState({
            feedbackGiven: true
        }, () => {
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
     */
    closeDialog() {
        this.setState({
            showDialog: false
        });
    }

    /**
     * Shows the comment dialog.
     */
    showDialogComment() {
        this.setState({
            showDialog: 'COMMENT'
        });
    }

    /**
     * Shows the email dialog.
     */
    showDialogEmail() {
        this.setState({
            showDialog: 'EMAIL'
        });
    }

    /**
     * Handles header button click event.
     */
    onHeaderAction(action) {
        switch (action) {
            case HeaderActions.COMMENT:
                this.showDialogComment();
                break;
            case HeaderActions.SUBMIT_EMAIL:
            case HeaderActions.SUBMIT_COMMENT:
                this.dialogForm && this.dialogForm.submit();
                break;
            case HeaderActions.FINISH:
                this.onFinish();
                break;
            case HeaderActions.CLOSE_DIALOG:
                if (this.state.showDialog === 'COMMENT' &&
                    brv.feedback.session.getRemainingCount() === 0) {
                    /* Closing comment but no aspects remaining. Consider this finished. */
                    this.onFinish();
                    return;
                }
                this.closeDialog();
                break;
        }
    }

    /**
     * On finish event.
     */
    onFinish() {
        if ((this.props.data.template_location === 1 && brv.feedback.session.hasPoor()) ||
            this.props.data.template_location === 2) {
            this.showDialogEmail();
        } else {
            /* Do not show email dialog. Complete the session. */
            this.completeSession();
        }
    }

    /**
     * On email submitted event.
     */
    onEmailSubmit() {
        if (this.props.data.template_location === 3) {
            this.closeDialog();
        } else {
            this.completeSession();
        }
    }

    /**
     * On comment submitted event.
     */
    onCommentSubmit() {
        this.setState({
            commentGiven: true
        }, () => this.closeDialog());
    }

    /**
     * Gets the dialog according to the current environment (mainly
     * showDialog state).
     */
    getDialog() {
        if (!this.state.showDialog) return null;

        switch(this.state.showDialog) {
            case 'COMMENT':
                return (
                    <CommentDialog
                        form={f => this.dialogForm = f}
                        session={this.state.session}
                        message={this.props.data.comment_message}
                        onValid={()=>this.setState({ pendingComment: true })}
                        onInvalid={()=>this.setState({ pendingComment: false })}
                        onSubmit={this.onCommentSubmit}
                    />
                );
            case 'EMAIL':
                return (
                    <EmailDialog
                        form={f => this.dialogForm = f}
                        session={this.state.session}
                        onSubmit={this.onEmailSubmit}
                    />
                );
        }

        return null;
    }

    render() {
        const dialogClass = (this.state.showDialog || 'none').toLowerCase();

        if (this.state.done) {
            return (
                <div className={`ly flex-v defined-size feedback-container dialog-none state-done`}>
                    <MinyHeader name={this.props.data.name} />
                    <div className='thanks'>
                        <span>Thank you for giving feedback!</span>
                        <div
                            className='btn btn-refresh'
                            onClick={this.reset}>
                            <i className={`fa fa-refresh`}></i>
                        </div>
                    </div>
                </div>
            );
        }

        return (
            <div
                key={this.state.reset}
                className={`ly flex-v defined-size feedback-container dialog-${dialogClass}`}>
                <Header
                    name={this.props.data.name}
                    onAction={this.onHeaderAction}
                    showDialog={this.state.showDialog}
                    enableComments={this.props.data.allow_comments}
                    enableSubmit={
                        (this.state.showDialog == 'COMMENT' && this.state.pendingComment) ||
                        this.state.showDialog == 'EMAIL' ||
                        (!this.state.showDialog && this.state.feedbackGiven)
                    }
                />
                <div className='scrollable'>
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
