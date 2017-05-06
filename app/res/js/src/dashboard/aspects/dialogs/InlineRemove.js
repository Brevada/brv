import React from 'react';
import Form, { Group } from 'forms/Form';
import { Button } from 'forms/inputs/Button';

/**
 * Inline dialog to display when user initiates removal of an aspect.
 *
 * @param {object} props
 * @param {string} title The name of the aspect to be deleted.
 * @param {number} id The id of the aspect to be deleted.
 * @param {function} onSuccess Callback to be invoked upon successful delete.
 * @param {function} onCancel Callback to be invoked upon cancel.
 */
const InlineRemove = props => (
    <div className='body'>
        <div className='remove-dialog'>
            <span>
                Are you sure? This means you will no longer collect feedback
                on <span className='aspect-name'>{props.title}</span>.
            </span>
            <Form
                method="DELETE"
                action={`/api/aspect/${props.id}`}
                onSuccess={props.onSuccess}
                onError={()=>false}>
                <Group
                    className='link-style'>
                    <Button
                        label="Remove"
                        submit={true}
                        right={true} />
                    <Button
                        label="Cancel"
                        left={true}
                        onClick={props.onCancel} />
                </Group>
            </Form>
        </div>
    </div>
);

export { InlineRemove };
