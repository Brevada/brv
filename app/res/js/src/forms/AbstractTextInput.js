import React from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import classNames from 'classnames';

import Input from 'forms/Input';

/**
 * A text input abstraction.
 */
export default class AbstractTextInput extends Input {

    static propTypes = {
        requireAttempt: PropTypes.bool,
        requireAlways: PropTypes.bool,
        name: PropTypes.string,
        type: PropTypes.string,
        placeHolder: PropTypes.string
    };

    constructor(props) {
        super(props);

        this.state = {
            /* The input's value. */
            value: '',

            /* Attempted */
            attempted: false
        };

        this.onFocus = ::this.onFocus;
        this.onBlur = ::this.onBlur;
        this.onChange = ::this.onChange;
    }

    componentDidMount() {
        /* Passes reference to self to parent. */
        if (this.props.input) {
            this.props.input(this);
        }
    }

    onFocus() {
        /* On focus, direct focus to nested DOM element. */
        this._input && ReactDOM.findDOMNode(this._input).focus();
    }

    onBlur() {
        /* If there was an attempt at data entry, make note of it for use with
         * CSS validation styling. */
        if (this._input && this.props.requireAttempt && this.state.value.length > 0) {
            this.setState({ attempted: true });
        }
    }

    onChange(e) {
        /* Save the input's value to the state. */
        this.setState({
            value: (e.target && e.target.value) || e.value || ''
        }, () => {
            if (this.props.onChange) this.props.onChange(this.state.value);
        });
    }

    getPlaceholder() {
        return this.state.value.length === 0 && this.props.placeHolder && (
            <div className='placeholder'>{ this.props.placeHolder || '' }</div>
        );
    }

    getInternalProps() {
        const passedProps = this.props.props || {};
        const req = (this.props.requireAlways || this.props.requireAttempt) ?
                  { required: true } : {};

        return Object.assign({
            className: classNames('textbox', {
                'seamless': this.props.seamless === true
            }),
            name: this.props.name,
            onChange: this.onChange,
            value: this.state.value,
            ref: input => (this._input = input)
        }, passedProps, req);
    }

    getContainerProps() {
        return {
            tabIndex: 0,
            onFocus: this.onFocus,
            onBlur: this.onBlur
        };
    }
}
