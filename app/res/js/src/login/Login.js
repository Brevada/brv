import React, { Component } from "react";

import Form, { Group, Label, Textbox, ErrorMessage } from "forms/Form";
import { Button } from "forms/inputs/Button";

/**
 * Login form.
 */
export default class Login extends Component {
    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {};

        this.onError = ::this.onError;
    }

    /**
     * Event handler for login success.
     * Redirect user to dashboard.
     * @returns {void}
     */
    static onSuccess() {
        window.location.replace("/dashboard");
    }

    /**
     * Event handler for login error.
     * @param   {object} error API error response.
     * @returns {void}
     */
    onError(error) {
        if (!error.data) error.data = {};
        this.setState({
            error: error.data.reason ||
            `Unknown error: ${error.status}`
        });
    }

    /**
     * @override
     */
    render() {
        const errMsg = this.state.error && (<ErrorMessage text={this.state.error} />);

        return (
            <div className="login-container">
                {errMsg}
                <Form
                    action="/login"
                    onSuccess={this.constructor.onSuccess}
                    onError={this.onError}>
                    <Group>
                        <Label text="Email" />
                        <Textbox
                            name="email"
                            placeHolder="Email"
                            type="email"
                            requireAttempt={true}
                            props={{
                                autoFocus: true
                            }}
                        />
                    </Group>
                    <Group>
                        <Label text="Password" />
                        <Textbox
                            name="password"
                            placeHolder="Password"
                            type="password"
                            requireAttempt={true}
                        />
                    </Group>
                    <Group>
                        <Button label="Login" right={true} submit={true} />
                    </Group>
                </Form>
            </div>
        );
    }

}
