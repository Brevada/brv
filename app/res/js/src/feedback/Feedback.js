import React from 'react';

import Header, { HeaderActions } from 'feedback/Header';
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

    constructor() {
        super();

        this.state = {
            /* Indicates whether at least one aspect has been rated. */
            feedbackGiven: false,

            /* Determines which dialog to show if any. */
            showDialog: false
        };

        /* Contains reference to current/last dialog's form.
         * Not part of state, since it doesn't affect render and should
         * not trigger redraw. */
        this.dialogForm = null;

        this.onAspectSubmitted = ::this.onAspectSubmitted;
        this.onHeaderAction = ::this.onHeaderAction;
        this.closeDialog = ::this.closeDialog;
        this.showDialogComment = ::this.showDialogComment;

        this.getDialog = ::this.getDialog;
    }

    /**
     * Handler for when feedback has been submitted for an aspect.
     * Occurs after actual submission to storage/network.
     */
    onAspectSubmitted() {
        if (this.state.feedbackGiven) return;

        /* At least one aspect has been rated. */
        this.setState({
            feedbackGiven: true
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
                break;
            case HeaderActions.CLOSE_DIALOG:
                this.closeDialog();
                break;
        }
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
                        message={this.props.data.comment_message}
                        form={f => this.dialogForm = f}
                    />
                );
            case 'EMAIL':
                return (
                    <EmailDialog
                        form={f => this.dialogForm = f}
                    />
                );
        }

        return null;
    }

    render() {
        const dialogClass = (this.state.showDialog || 'none').toLowerCase();

        return (
            <div className={`ly flex-v defined-size feedback-container dialog-${dialogClass}`}>
                <Header
                    name={this.props.data.name}
                    onAction={this.onHeaderAction}
                    showDialog={this.state.showDialog}
                    enableFinish={this.state.feedbackGiven}
                />
                <div className='scrollable'>
                    { this.getDialog() }
                    <Aspects
                        aspects={this.props.data.aspects}
                        onSubmit={this.onAspectSubmitted}
                    />
                </div>
            </div>
        );
    }

}
