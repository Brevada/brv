import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

import Input from "forms/Input";

/**
 * A text input abstraction.
 */
export default class AbstractTextInput extends Input {

    static propTypes = {
        /* Only enforce DOM require validation after user has "attempted"
         * input in the form. */
        requireAttempt: PropTypes.bool,

        /* Always enforce DOM require validation, even before user has
         * entered data. */
        requireAlways: PropTypes.bool,

        /* Key for form data object submitted to API. */
        name: PropTypes.string,

        /* DOM Input type, e.g. password, text, email, ... */
        type: PropTypes.string,

        /* Placeholder text when no focus and no input. */
        placeHolder: PropTypes.string
    };

    /**
     * @constructor
     * @param  {object} props React props.
     */
    constructor(props) {
        super(props);

        this.state = {

            /* The input's value. */
            value: "",

            /* Attempted */
            attempted: false
        };

        this.onFocus = ::this.onFocus;
        this.onBlur = ::this.onBlur;
        this.onChange = ::this.onChange;
    }

    /**
     * @override
     */
    componentDidMount() {
        /* Passes reference to self to parent. */
        if (this.props.input) this.props.input(this);
    }

    /**
     * Handles onFocus event, triggered when focus is given to the input.
     * @returns {void}
     */
    onFocus() {
        /* On focus, direct focus to nested DOM element. */
        if (this._input) this._input.focus();
    }

    /**
     * Handles onBlur event, triggered when focus is taken from the input.
     * @returns {void}
     */
    onBlur() {
        /* If there was an attempt at data entry, make note of it for use with
         * CSS validation styling. */
        if (this._input && this.props.requireAttempt && this.state.value.length > 0) {
            this.setState({ attempted: true });
        }
    }

    /**
     * Handles text entry in input, recording new data/state when entered.
     * @param   {Event} e onChange event object.
     * @returns {void}
     */
    onChange(e) {
        /* Save the input's value to the state. */
        this.setState({
            value: (e.target && e.target.value) || e.value || ""
        }, () => {
            if (this.props.onChange) this.props.onChange(this.state.value);
        });
    }

    /**
     * Gets the placeholder JSX.
     * @returns {JSX}
     */
    getPlaceholder() {
        if (this.state.value.length === 0 && this.props.placeHolder) {
            return (
                <div className="placeholder">{ this.props.placeHolder || "" }</div>
            );
        }

        return null;
    }

    /**
     * Constructs and returns default/base Input React properties.
     * @returns {object}
     */
    getInternalProps() {
        const passedProps = this.props.props || {};
        const req = (this.props.requireAlways || this.props.requireAttempt) ?
                  { required: true } :
                  {};

        return Object.assign({
            className: classNames("textbox", {
                "seamless": Boolean(this.props.seamless)
            }),
            name: this.props.name,
            onChange: this.onChange,
            value: this.state.value,
            ref: input => {
                this._input = input;
            }
        }, passedProps, req);
    }

    /**
     * Constructs and returns default/base Input container React properties.
     * @returns {object}
     */
    getContainerProps() {
        return {
            tabIndex: 0,
            onFocus: this.onFocus,
            onBlur: this.onBlur
        };
    }
}
