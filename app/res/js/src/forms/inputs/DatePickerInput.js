import React from 'react';
import ReactDOM from 'react-dom';
import DatePicker from 'react-datepicker';

import Input from 'forms/Input';

/**
 * Date Picker Input based on react-datepicker
 */
export default class DatePickerInput extends Input {
    constructor(props) {
        super(props);

        this.state = {
            unix: -1,
            date: null
        };

        if (props.defaultDate) {
            this.state.date = props.defaultDate;
            this.state.unix = props.defaultDate.unix();
        }

        this.onChange = ::this.onChange;
    }

    /**
     * Date change event handler.
     * @param {moment} date
     */
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
