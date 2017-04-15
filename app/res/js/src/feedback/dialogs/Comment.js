import React from 'react';
import Dialog from 'feedback/Dialog';
import Form, { Group, Textarea } from 'forms/Form';

/**
 * Comment dialog.
 */
export default class Comment extends React.Component {

    static propTypes = {

    };

    constructor() {
        super();
        this.state = {};

        this.validate = ::this.validate;
        this.focusToInput = ::this.focusToInput;
    }

    /**
     * Validates comment text, and if valid, allows user to submit.
     */
    validate(value) {
        /* Arbitrary min. length check. */
        if (value && value.trim().length > 2) {
            if (this.props.onValid) this.props.onValid();
        } else {
            if (this.props.onInvalid) this.props.onInvalid();
        }
    }

    /**
     * Sends user focus to input.
     */
    focusToInput() {
        this._textarea && this._textarea.onFocus();
    }

    render() {
        return (
            <Dialog
                className='dialog-comment'
                onAttention={this.focusToInput}>
                <Form
                    center={true}
                    method="POST"
                    action="/api/feedback/comment"
                    data={{
                        store: brv.feedback.id() || false,
                        session: this.props.session
                    }}
                    form={this.props.form}
                    onSuccess={()=>this.props.onSubmit()}>
                    <Group>
                        <Textarea
                            seamless={true}
                            onChange={this.validate}
                            input={v => this._textarea = v}
                            name='comment'
                            props={{
                                autoFocus: true,
                                placeholder: this.props.message || "Write us a comment..."
                            }}
                        />
                    </Group>
                </Form>
            </Dialog>
        );
    }

}
