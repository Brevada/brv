import React, { Component } from "react";
import PropTypes from "prop-types";

import Fetch from "forms/Fetch";
import Dialog, { DialogButtonActions } from "dialogs/Dialog";
import Form, { Group, Label, ErrorMessage } from "forms/Form";
import { Button } from "forms/inputs/Button";
import AspectInputField from "forms/inputs/AspectInputField";

/**
 * Connects Aspect Input Field with Data Layer.
 *
 * @param   {object} props React props
 * @param   {object} props.data Data passed from data layer.
 * @returns {JSX}
 */
const FetchedAspectInputField = props => (
    <AspectInputField
        types={props.data.aspect_types || []}
        name="aspect"
        custom={true}
        placeHolder="type something here"
    />
);

FetchedAspectInputField.propTypes = {
    data: PropTypes.object
};

FetchedAspectInputField.defaultProps = {
    data: {}
};

/**
 * New aspect dialog.
 */
export default class NewAspect extends Component {

    static propTypes = {
        onAction: PropTypes.func.isRequired,
        show: PropTypes.bool,
        storeId: PropTypes.number.isRequired
    };

    static defaultProps = {
        show: false
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {
            createError: null
        };

        this.submitError = ::this.submitError;
        this.onSuccess = ::this.onSuccess;
        this.closeDialog = ::this.closeDialog;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Event handler for form submission error.
     * @param   {Error} error Failed submission error
     * @returns {void}
     */
    submitError(error) {
        if (this._unmounted) return;
        this.setState({
            createError: error.data.reason ||
            `Unknown error: ${error.status}`
        });
    }

    /**
     * Closes new aspect dialog, having added new aspect.
     * @returns {void}
     */
    onSuccess() {
        this.props.onAction(DialogButtonActions.SUCCESS);
    }

    /**
     * Closes new aspect dialog.
     * @returns {void}
     */
    closeDialog() {
        this.props.onAction(DialogButtonActions.CLOSE);
    }

    /**
     * @override
     */
    render() {
        return (
            <Dialog
                defaultShow={this.props.show}
                onAction={this.props.onAction}
                escapable={true}>
                <Form
                    method="POST"
                    action="/api/aspect"
                    data={{ store: this.props.storeId }}
                    onSuccess={this.onSuccess}
                    onError={this.submitError}>
                    { this.state.createError !== null && (
                        <ErrorMessage text={this.state.createError} />
                    ) }

                    <Group className="new-aspect">
                        <Label
                            text={"How satisfied are customers with"}
                            inline={true}
                        />
                        <Fetch
                            action="/api/aspecttypes/industry"
                            data={{exclude_store: this.props.storeId}}>
                            <FetchedAspectInputField />
                        </Fetch>
                        <Label
                            text={"?"}
                            inline={true}
                        />
                    </Group>
                    <Group className="dialog-controls link-style">
                        <Button
                            label="Add Question"
                            submit={true}
                            right={true}
                        />
                        <Button
                            label="Cancel"
                            onClick={this.closeDialog}
                            right={true}
                        />
                    </Group>
                </Form>
            </Dialog>
        );
    }
}
