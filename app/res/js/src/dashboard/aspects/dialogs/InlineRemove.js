import React from "react";
import PropTypes from "prop-types";
import Form, { Group } from "forms/Form";
import { Button } from "forms/inputs/Button";

/**
 * Inline dialog to display when user initiates removal of an aspect.
 *
 * @param {object} props Properties.
 * @param {string} props.title The name of the aspect to be deleted.
 * @param {number} props.id The id of the aspect to be deleted.
 * @param {function} props.onSuccess Callback to be invoked upon successful delete.
 * @param {function} props.onCancel Callback to be invoked upon cancel.
 *
 * @returns {JSX}
 */
const InlineRemove = props => (
    <div className="body">
        <div className="remove-dialog">
            <span>
                {"Are you sure? This means you will no longer collect " +
                 "feedback on "}
                <span className="aspect-name">{props.title}</span>
                {"."}
            </span>
            <Form
                method="DELETE"
                action={`/api/aspect/${props.id}`}
                onSuccess={props.onSuccess}
                onError={props.onError}>
                <Group
                    className="link-style">
                    <Button
                        label="Remove"
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
    title: PropTypes.string.isRequired,
    id: PropTypes.number.isRequired,
    onSuccess: PropTypes.func.isRequired,
    onError: PropTypes.func,
    onCancel: PropTypes.func.isRequired
};

InlineRemove.defaultProps = {
    onError: () => { /* noop */ }
};

export { InlineRemove };
