/* global brv */

import React, { Component } from "react";
import PropTypes from "prop-types";
import Dialog from "feedback/Dialog";
import Form, { Group, Label } from "forms/Form";
import IntelliEmailInput from "forms/inputs/IntelliEmailInput";
import Toggle from "forms/inputs/Toggle";

/**
 * Email dialog.
 */
export default class Email extends Component {

    static propTypes = {
        onSubmit: PropTypes.func.isRequired,
        session: PropTypes.string.isRequired,
        form: PropTypes.func.isRequired
    };

    /**
     * @constructor
     */
    constructor() {
        super();
        this.state = {};
    }

    /**
     * @override
     */
    render() {
        return (
            <Dialog
                className="dialog-email as-page">
                <Form
                    center={true}
                    method="POST"
                    action="/api/feedback/email"
                    data={{
                        store: brv.feedback.id() || false,
                        session: this.props.session
                    }}
                    form={this.props.form}
                    onSuccess={this.props.onSubmit}>
                    <Group className="email">
                        <IntelliEmailInput
                            name="email"
                            domains={[
                                "gmail.com",
                                "hotmail.com",
                                "outlook.com",
                                "yahoo.com"
                            ]}
                        />
                    </Group>
                    <div className="consents">
                        <Group className="toggle consent">
                            <Label text="I would like to be contacted about my experience" />
                            <Toggle
                                positiveLabel="Yes"
                                negativeLabel="No"
                                default={true}
                                name="contact_consent"
                            />
                        </Group>
                        <Group className="toggle subscribe">
                            <Label text="Send me promotional offers and updates" />
                            <Toggle
                                positiveLabel="Yes"
                                negativeLabel="No"
                                default={true}
                                name="subscribe"
                            />
                        </Group>
                    </div>
                </Form>
            </Dialog>
        );
    }

}
