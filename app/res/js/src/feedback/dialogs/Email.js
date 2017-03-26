import React from 'react';
import Dialog from 'feedback/Dialog';
import Form, { Group, Label } from 'forms/Form';
import IntelliEmailInput from 'forms/IntelliEmailInput';
import Toggle from 'forms/Toggle';

/**
 * Email dialog.
 */
export default class Email extends React.Component {

    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <Dialog
                className='dialog-email as-page'>
                <Form
                    center={true}
                    form={this.props.form}>
                    <Group className='email'>
                        <IntelliEmailInput
                            name='email'
                            domains={[
                                'gmail.com',
                                'hotmail.com',
                                'outlook.com',
                                'yahoo.com'
                            ]}
                        />
                    </Group>
                    <Group className='toggle consent'>
                        <Label text='I would like to be contacted about my experience' />
                        <Toggle
                            positiveLabel='Yes'
                            negativeLabel='No'
                            default={true}
                            name='contact_consent'
                        />
                    </Group>
                    <Group className='toggle subscribe'>
                        <Label text='Send me promotional offers and updates' />
                        <Toggle
                            positiveLabel='Yes'
                            negativeLabel='No'
                            default={true}
                            name='subscribe'
                        />
                    </Group>
                </Form>
            </Dialog>
        );
    }

}
