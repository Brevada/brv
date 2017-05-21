import React from "react";
import DatePicker from "react-datepicker";

import Input from "forms/Input";

/**
 * Date Picker Input based on react-datepicker
 */
export default class DatePickerInput extends Input {
    /**
     * @constructor
     * @param  {object} props React props.
     */
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
        this.onFocus = ::this.onFocus;
        this.saveRef = ::this.saveRef;
    }

    /**
     * Date change event handler.
     * @param   {moment} date Date captured from date picker.
     * @returns {void}
     */
    onChange(date) {
        this.setState({
            date,
            unix: date === null ? date : date.unix()
        }, () => {
            if (this.props.onDateChange) {
                this.props.onDateChange(date);
            }
        });
    }

    /**
     * Handles onFocus event.
     * @returns {void}
     */
    onFocus() {
        this._inputPicker && this._inputPicker.handleFocus();
    }

    /**
     * Saves reference to input.
     * @param   {DOMElement} input The input's DOM element
     * @returns {void}
     */
    saveRef(input) {
        this._inputPicker = input;
    }

    /**
     * @override
     */
    render() {
        return (
            <div
                className="input datepicker"
                tabIndex={0}
                onFocus={this.onFocus}>
                <DatePicker
                    ref={this.saveRef}
                    selected={this.state.date}
                    onChange={this.onChange}
                    className="datepicker-input"
                    placeholderText={this.props.placeHolder || ""}
                    {...this.props}
                />
                <input
                    type="hidden"
                    value={this.state.unix || -1}
                    name={`${this.props.name}_unix`}
                />
            </div>
        );
    }
}
