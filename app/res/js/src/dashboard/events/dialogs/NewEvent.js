import React from 'react';
import moment from 'moment';
import DataLayer from 'forms/DataLayer';

import Dialog, { DialogButtonActions } from 'dialogs/Dialog';
import Form, { Group as FormGroup, Label as FormLabel, Textbox, Button, ErrorMessage } from 'forms/Form';
import DatePickerInput from 'forms/DatePickerInput';

/**
 * New event dialog.
 */
export default class NewEvent extends React.Component {
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
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * New event submission error handler.
     */
    submitError(error) {
        if (this._unmounted) return;
        this.setState({
            createError: error.data.reason ||
            `Unknown error: ${error.status}`
        });
    }

    render() {
        return (
            <Dialog
                defaultShow={this.props.show}
                onAction={this.props.onAction}
                escapable={true}
            >
                <Form
                    method="POST"
                    action="/api/event"
                    data={{ store: this.props.storeId }}
                    onSuccess={()=>this.props.onAction(DialogButtonActions.SUCCESS)}
                    onError={this.submitError}
                    className='center'
                >
                    { this.state.createError !== null && (
                        <ErrorMessage text={this.state.createError} />
                    ) }

                    <FormGroup className='new-event'>
                        <FormLabel
                            text={'What is the title of the event?'}
                            inline={true}
                        />
                        <Textbox
                            placeHolder={'e.g. Hired a New Chef'}
                            name='title'
                            seamless={true}
                        />
                    </FormGroup>
                    <FormGroup className='date inline left'>
                        <FormLabel
                            text={'Choose a start date:'}
                        />
                        <DatePickerInput
                            name='from'
                            defaultDate={moment()}
                            selectsStart
                            startDate={this.state.fromDate}
                            endDate={this.state.toDate}
                            onDateChange={d=>this.setState({ fromDate: d })}
                            isClearable={false} />
                    </FormGroup>
                    <FormGroup className='date inline right'>
                        <FormLabel
                            text={'Choose an optional end date:'}
                        />
                        <DatePickerInput
                            name='to'
                            selectsEnd
                            startDate={this.state.fromDate}
                            endDate={this.state.toDate}
                            onDateChange={d=>this.setState({ toDate: d })}
                            isClearable={true} />
                    </FormGroup>
                    <FormGroup className='dialog-controls link-style'>
                        <Button
                            label='Add Event'
                            submit={true}
                            right={true}
                        />
                        <Button
                            label='Cancel'
                            onClick={()=>this.props.onAction(DialogButtonActions.CLOSE)}
                            right={true}
                        />
                    </FormGroup>
                </Form>
            </Dialog>
        );
    }
}
