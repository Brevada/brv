import React from 'react';

import Form, { Group, Label, Textbox, Button, ErrorMessage } from 'forms/Form';
import { Link } from 'components/Link';

/**
 * Login form.
 */
export default class Login extends React.Component {
    constructor() {
        super();

        this.state = {};

        this.onSuccess = ::this.onSuccess;
        this.onError = ::this.onError;
    }

    /**
     * Event handler for login success.
     * Redirect user to dashboard.
     */
    onSuccess(response) {
        // TODO: Interesting loading graphic.
        window.location.replace('/dashboard');
    }

    /**
     * Event handler for login error.
     */
    onError(error) {
        if (!error.data) error.data = {};
        this.setState({
            error: error.data.reason ||
            `Unknown error: ${error.status}`
        });
    }

    render() {
        return (
            <div className='login-container'>
                { this.state.error && (
                    <ErrorMessage text={this.state.error} />
                ) }
                <Form
                    action='/login'
                    onSuccess={this.onSuccess}
                    onError={this.onError}>
                    <Group>
                        <Label text='Email' />
                        <Textbox
                            name='email'
                            placeHolder='Email'
                            type='email'
                            props={{
                                autoFocus: true
                            }}
                        />
                    </Group>
                    <Group>
                        <Label text='Password' />
                        <Textbox
                            name='password'
                            placeHolder='Password'
                            type='password'
                        />
                    </Group>
                    <Group>
                        <Link label='Forgot your password?' left={true} btnLike={true} />
                        <Button label='Login' right={true} submit={true} />
                    </Group>
                </Form>
            </div>
        );
    }

}
