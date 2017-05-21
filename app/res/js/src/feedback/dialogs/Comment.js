/* global brv */

import React, { Component } from "react";
import PropTypes from "prop-types";
import Dialog from "feedback/Dialog";
import Form, { Group, Textarea } from "forms/Form";

/**
 * Comment dialog.
 */
export default class Comment extends Component {

    static propTypes = {
        onValid: PropTypes.func,
        onInvalid: PropTypes.func,
        onSubmit: PropTypes.func.isRequired,
        session: PropTypes.string.isRequired,
        form: PropTypes.func.isRequired,
        message: PropTypes.string
    };

    static defaultProps = {
        onValid: null,
        onInvalid: null,
        message: "Write us a comment..."
    };

    /**
     * @constructor
     */
    constructor() {
        super();
        this.state = {};

        this.validate = ::this.validate;
        this.focusToInput = ::this.focusToInput;
        this.saveInputRef = ::this.saveInputRef;
    }

    /**
     * Validates comment text, and if valid, allows user to submit.
     * @param   {string} value Comment text
     * @returns {void}
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
     * @returns {void}
     */
    focusToInput() {
        this._textarea && this._textarea.onFocus();
    }

    /**
     * Saves reference to input DOM element
     * @param   {DOMElement} input Input reference to save.
     * @returns {void}
     */
    saveInputRef(input) {
        this._textarea = input;
    }

    /**
     * @override
     */
    render() {
        return (
            <Dialog
                className="dialog-comment"
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
                    onSuccess={this.props.onSubmit}>
                    <Group>
                        <Textarea
                            seamless={true}
                            onChange={this.validate}
                            input={this.saveInputRef}
                            name="comment"
                            props={{
                                autoFocus: true,
                                placeholder: this.props.message ||
                                             Comment.defaultProps.message
                            }}
                        />
                    </Group>
                </Form>
            </Dialog>
        );
    }

}
