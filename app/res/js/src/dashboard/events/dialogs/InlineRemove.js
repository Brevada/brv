import React from 'react';
import Form, { Group, Button, Group as FormGroup } from 'forms/Form';

/**
 * Inline dialog to display when user initiates removal of an event.
 *
 * @param {object} props
 * @param {string} title The name of the event to be deleted.
 * @param {number} id The id of the event to be deleted.
 * @param {function} onSuccess Callback to be invoked upon successful delete.
 * @param {function} onCancel Callback to be invoked upon cancel.
 */
const InlineRemove = props => (
    <div className='body'>
        <div className='remove-dialog'>
            <span>
                Are you sure you would like to delete this event?
            </span>
            <Form
                method="DELETE"
                action={`/api/event/${props.id}`}
                onSuccess={props.onSuccess}
                onError={()=>false}>
                <FormGroup
                    className='link-style'>
                    <Button
                        label="Delete"
                        submit={true}
                        right={true} />
                    <Button
                        label="Cancel"
                        left={true}
                        onClick={props.onCancel} />
                </FormGroup>
            </Form>
        </div>
    </div>
);

export { InlineRemove };
