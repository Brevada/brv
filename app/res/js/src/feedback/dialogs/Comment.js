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
    }

    render() {
        return (
            <Dialog className='dialog-comment'>
                <Form
                    center={true}
                    form={this.props.form}>
                    <Group>
                        <Textarea
                            seamless={true}
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
