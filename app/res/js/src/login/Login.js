import React from 'react';
import ReactDOM from 'react-dom';

import Form, { Group, Label, Textbox, Button, ErrorMessage } from '../forms/Form';
import { Link } from '../components/Link';

export default class Login extends React.Component {
    constructor() {
        super();

        this.state = {};

        this.onSuccess = this.onSuccess.bind(this);
        this.onError = this.onError.bind(this);
    }

    onSuccess(response) {
        // TODO: Interesting loading graphic.
        window.location.replace('/dashboard');
    }

    onError(error) {
        if (!error.data) error.data = {};
        this.setState({ error: error.data.reason || `Unknown error: ${error.status}` });
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
