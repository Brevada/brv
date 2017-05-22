import React, { Component } from "react";
import PropTypes from "prop-types";
import moment from "moment";

import Dialog, { DialogButtonActions } from "dialogs/Dialog";
import Form, { Group, Label, Textbox, ErrorMessage } from "forms/Form";
import { Button } from "forms/inputs/Button";
import DatePickerInput from "forms/inputs/DatePickerInput";

/**
 * New event dialog.
 */
export default class NewEvent extends Component {

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
     * @param   {object} props React props
     */
    constructor() {
        super();

        this.state = {
            /* error message from failed submission */
            createError: null,

            /* start date from user input */
            fromDate: null,

            /* to date from user input (optional) */
            toDate: null
        };

        this.submitError = ::this.submitError;
        this.closeDialog = ::this.closeDialog;
        this.onSuccess = ::this.onSuccess;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * New event submission error handler.
     * @param   {Event} error Submission error
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
     * Close dialog, having created new event.
     * @returns {void}
     */
    onSuccess() {
        this.props.onAction(DialogButtonActions.SUCCESS);
    }

    /**
     * Close dialog.
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
                    action="/api/event"
                    data={{ store: this.props.storeId }}
                    onSuccess={this.onSuccess}
                    onError={this.submitError}
                    className="center">
                    { this.state.createError !== null && (
                        <ErrorMessage text={this.state.createError} />
                    ) }

                    <Group className="new-event">
                        <Label
                            text={"What is the title of the event?"}
                            inline={true}
                        />
                        <Textbox
                            placeHolder={"e.g. Hired a New Chef"}
                            name="title"
                            seamless={true}
                        />
                    </Group>
                    <Group className="date inline left">
                        <Label
                            text={"Choose a start date:"}
                        />
                        <DatePickerInput
                            name="from"
                            defaultDate={moment()}
                            selectsStart={true}
                            startDate={this.state.fromDate}
                            endDate={this.state.toDate}
                            onDateChange={d => ( // eslint-disable-line react/jsx-no-bind
                                this.setState({ fromDate: d })
                            )}
                            isClearable={false}
                        />
                    </Group>
                    <Group className="date inline right">
                        <Label
                            text={"Choose an optional end date:"}
                        />
                        <DatePickerInput
                            name="to"
                            selectsEnd={true}
                            startDate={this.state.fromDate}
                            endDate={this.state.toDate}
                            onDateChange={d => ( // eslint-disable-line react/jsx-no-bind
                                this.setState({ toDate: d })
                            )}
                            isClearable={true}
                        />
                    </Group>
                    <Group className="dialog-controls link-style">
                        <Button
                            label="Add Event"
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
