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
                    center={true}>
                    <Group>
                        <Textarea
                            seamless={true}
                            props={{
                                autoFocus: true,
                                placeholder: this.props.commentMessage || "Write us a comment..."
                            }}
                        />
                    </Group>
                </Form>
            </Dialog>
        );
    }

}
