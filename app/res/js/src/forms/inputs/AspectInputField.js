import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';

import { Input as FormInput } from 'forms/Form';

/**
 * Single aspect option.
 * @param {function} props.onClick On click event handler.
 */
const AspectOption = props => (
    <div className={'option ' + (props.className || '')} onMouseDown={props.onClick}>
        {props.label}
    </div>
);

/**
 * Aspect options drop down.
 */
const AspectOptions = props => (
    <div className='aspect-options'>
        { props.custom === true && props.id === -1 &&
            props.value.length > 0 && (
            <AspectOption
                label={`"${props.value.trim()}"`}
                className='new'
                onClick={() => {
                    props.onSelect({
                        id: -1,
                        title: props.value.trim()
                    });
                }}
            />
        ) }

        { props.types.filter(type => (
            props.value.length === 0 ||
            type.title.toLowerCase().indexOf(props.value.toLowerCase()) >= 0
        )).map(type => (
            <AspectOption
                key={type.id}
                label={type.title}
                onClick={()=>props.onSelect(type)}
            />
        )) }
    </div>
);

/**
 * Form input element to provide selection of aspect types.
 */
export default class AspectInputField extends FormInput {
    constructor(props) {
        super(props);

        this.state = {
            /* Value of aspect type (label). */
            value: '',

            /* Numeric id of aspect type. -1 means not set, or does not exist
            (in which case, it may be a custom type). */
            id: -1,

            /* Controls visibility of dropdown. */
            show: false,

            /* Aspect types to choose from. */
            types: props.types || []
        };

        this.onSelect = ::this.onSelect;
        this.onTextChange = ::this.onTextChange;
        this.onBlur = ::this.onBlur;
        this.onFocus = ::this.onFocus;
        this.onKeyPress = ::this.onKeyPress;
        this.potentialSubmit = ::this.potentialSubmit;
    }

    componentWillReceiveProps(nextProps) {
        /* Update state if aspect types differ. */

        if (!nextProps.types || _.isEqual(nextProps.types, this.state.types)) {
            return;
        }

        this.setState({
            types: nextProps.types
        });
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * If aspect item is selected, and submitOnSelect is true, then allow
     * submission of the form.
     */
    potentialSubmit() {
        if (this._inputText && this.props.submitOnSelect && this.props.form) {
            this.props.form().submit();
            if (!this._unmounted) {
                this.setState({
                    value: '',
                    id: -1,
                    show: false
                });
            }
        }
    }

    /**
     * onKeyPress event handler. Support form submission on enter.
     */
    onKeyPress(e) {
        if (e.key == 'Enter') {
            this.potentialSubmit();
        }
    }

    /**
     * Event handler for aspect selection.
     */
    onSelect(type) {
        if (this._unmounted) return;

        /* Update state and hide drop down. */
        this.setState({
            value: type.title,
            id: type.id,
            show: false
        }, this.potentialSubmit);
    }

    /**
     * Event handler for typing in the input. Update search/drop down.
     */
    onTextChange(e) {
        if (this._unmounted) return;

        let id = -1;
        if (this.state.value.length > 0) {
            let types = this.state.types.filter(type => (type.title.toLowerCase() == this.state.value.trim().toLowerCase()));
            if (types.length > 0) {
                id = types[0].id;
            }
        }

        this.setState({
            value: e.target.value,
            id: id
        });
    }

    /**
     * On focus, redirect focus to textbox and show drop down.
     */
    onFocus() {
        if (this._unmounted || this.state.show) return;

        this.setState({
            show: true
        }, () => {
            this._inputText && ReactDOM.findDOMNode(this._inputText).focus();
        });
    }

    /**
     * Blur event handler. Hide drop down.
     */
    onBlur() {
        if (this._unmounted || !this.state.show) return;

        this.setState({
            show: false
        });
    }

    render() {
        return (
            <div className='aspect-input' tabIndex={0} onFocus={this.onFocus}>
                <input
                    className='textbox'
                    name={this.props.name}
                    placeholder=''
                    type='text'
                    ref={ input => (this._inputText = input) }
                    onChange={this.onTextChange}
                    value={this.state.value}
                    onFocus={this.onFocus}
                    onBlur={this.onBlur}
                    onKeyPress={this.onKeyPress}
                    autoComplete='off'
                />
                { this.state.value.length === 0 && (
                    <div className='placeholder'>{ this.props.placeHolder || '' }</div>
                ) }
                <input
                    type='hidden'
                    value={this.state.id}
                    name={`${this.props.name}_id`}
                />
                { this.state.show && (
                    <AspectOptions
                        id={this.state.id}
                        custom={this.props.custom}
                        value={this.state.value}
                        types={this.state.types}
                        onSelect={this.onSelect}
                    />
                ) }
            </div>
        );
    }
}
