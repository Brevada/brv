import React from "react";
import PropTypes from "prop-types";
import Form, { Group } from "forms/Form";
import { Button } from "forms/inputs/Button";

/**
 * Inline dialog to display when user initiates removal of an event.
 *
 * @param   {object} props React props
 * @param   {string} props.title The name of the event to be deleted.
 * @param   {number} props.id The id of the event to be deleted.
 * @param   {function} props.onSuccess Callback to be invoked upon successful delete.
 * @param   {function} props.onCancel Callback to be invoked upon cancel.
 * @param   {function} props.onBegin Callback to be invoked upon start of deletion.
 * @returns {JSX}
 */
const InlineRemove = props => (
    <div className="body">
        <div className="remove-dialog">
            <span>
                {"Are you sure you would like to delete this event?"}
            </span>
            <Form
                method="DELETE"
                action={`/api/event/${props.id}`}
                onSuccess={props.onSuccess}
                onError={props.onError}
                onBegin={props.onBegin}>
                <Group
                    className="link-style">
                    <Button
                        label="Delete"
                        submit={true}
                        right={true}
                    />
                    <Button
                        label="Cancel"
                        left={true}
                        onClick={props.onCancel}
                    />
                </Group>
            </Form>
        </div>
    </div>
);

InlineRemove.propTypes = {
    id: PropTypes.number.isRequired,
    onSuccess: PropTypes.func.isRequired,
    onError: PropTypes.func.isRequired,
    onBegin: PropTypes.func.isRequired,
    onCancel: PropTypes.func.isRequired
};

export { InlineRemove };
