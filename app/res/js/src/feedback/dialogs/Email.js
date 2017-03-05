import React from 'react';
import Dialog from 'feedback/Dialog';

/**
 * Email dialog.
 */
export default class Email extends React.Component {

    static propTypes = {

    };

    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <Dialog>
                <Form
                    center={true}
                    form={this.props.form}>
                    <Group>
                        Placeholder...
                    </Group>
                </Form>
            </Dialog>
        );
    }

}
