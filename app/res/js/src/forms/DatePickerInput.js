import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import moment from 'moment';
import DatePicker from 'react-datepicker';

import { Input as FormInput } from 'forms/Form';

export default class DatePickerInput extends FormInput {
    constructor(props) {
        super(props);

        this.state = {
            unix: -1,
            date: null
        };

        if (typeof props.defaultDate !== undefined) {
            this.state.date = props.defaultDate;
            this.state.unix = props.defaultDate.unix();
        }

        this.onChange = this.onChange.bind(this);
    }

    onChange(date) {
        this.setState({
            date: date,
            unix: date === null ? date : date.unix()
        }, () => {
            if (this.props.onDateChange) {
                this.props.onDateChange(date);
            }
        });
    }

    render() {
        return (
            <div className='input datepicker' tabIndex={0} onFocus={()=>{
                this._inputPicker && ReactDOM.findDOMNode(this._inputPicker).focus();
            }}>
                <DatePicker
                    ref={ input => (this._inputPicker = input) }
                    selected={this.state.date}
                    onChange={this.onChange}
                    className='datepicker-input'
                    placeholderText={this.props.placeHolder || ''}
                    {...this.props}
                />
                <input
                    type='hidden'
                    value={this.state.unix || -1}
                    name={`${this.props.name}_unix`}
                />
            </div>
        );
    }
}
