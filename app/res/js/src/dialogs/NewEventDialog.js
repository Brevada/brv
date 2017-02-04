import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import moment from 'moment';
import DataLayer from 'forms/DataLayer';

import Dialog, { DialogButton, DialogButtonActions } from 'dialogs/Dialog';
import Form, { Group as FormGroup, Label as FormLabel, Textbox, Button, ErrorMessage } from 'forms/Form';
import DatePickerInput from 'forms/DatePickerInput';

import { Link } from 'components/Link';

export default class NewEventDialog extends React.Component {
    constructor() {
        super();

        this.state = {
            createError: null,

            fromDate: null,
            toDate: null
        };

        this.submitError = this.submitError.bind(this);
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    submitError(error) {
        if (this._unmounted) return;
        this.setState({ createError: error.data.reason || `Unknown error: ${error.status}` });
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
