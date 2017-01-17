import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import equal from 'deep-equal';

import { Input as FormInput } from 'forms/Form';

const AspectOption = props => (
    <div className={'option ' + (props.className || '')} onMouseDown={props.onClick}>
        {props.label}
    </div>
);

export default class AspectInputField extends FormInput {
    constructor(props) {
        super(props);

        this.state = {
            value: '',
            id: -1,
            show: false,
            types: props.types || []
        };

        this.onSelect = this.onSelect.bind(this);
        this.onTextChange = this.onTextChange.bind(this);
        this.onBlur = this.onBlur.bind(this);
        this.onFocus = this.onFocus.bind(this);
        this.onKeyPress = this.onKeyPress.bind(this);
        this.potentialSubmit = this.potentialSubmit.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        if (!nextProps.types || equal(nextProps.types, this.state.types)) {
            return;
        }

        this.setState({
            types: nextProps.types
        });
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

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

    onKeyPress(e) {
        if (e.key == 'Enter') {
            this.potentialSubmit();
        }
    }

    onSelect(type) {
        if (this._unmounted) return;

        this.setState({
            value: type.title,
            id: type.id,
            show: false
        }, this.potentialSubmit);
    }

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

    onFocus() {
        if (this._unmounted || this.state.show) return;

        this.setState({
            show: true
        });
    }

    onBlur() {
        if (this._unmounted || !this.state.show) return;

        this.setState({
            show: false
        });
    }

    render() {
        return (
            <div className='aspect-input' tabIndex={0} onFocus={()=>{
                this._inputText && ReactDOM.findDOMNode(this._inputText).focus();
            }}>
                <input
                    className='textbox'
                    name={this.props.name}
                    placeholder={this.props.placeHolder || ''}
                    type='text'
                    ref={ input => (this._inputText = input) }
                    onChange={this.onTextChange}
                    value={this.state.value}
                    onFocus={this.onFocus}
                    onBlur={this.onBlur}
                    onKeyPress={this.onKeyPress}
                    autoComplete='off'
                />
                <input
                    type='hidden'
                    ref={ input => (this._inputId = input) }
                    value={this.state.id}
                    name={`${this.props.name}_id`}
                />
                { this.state.show && (
                    <div className='aspect-options'>
                        { this.props.custom === true && this.state.id === -1 &&
                            this.state.value.length > 0 && (
                            <AspectOption
                                label={`"${this.state.value.trim()}"`}
                                className='new'
                                onClick={() => {
                                    this.onSelect({
                                        id: -1,
                                        title: this.state.value.trim()
                                    });
                                }}
                            />
                        ) }

                        { this.state.types.filter(type => (
                            this.state.value.length === 0 ||
                            type.title.toLowerCase().indexOf(this.state.value.toLowerCase()) >= 0
                        )).map(type => (
                            <AspectOption
                                key={type.id}
                                label={type.title}
                                onClick={()=>this.onSelect(type)}
                            />
                        )) }
                    </div>
                ) }
            </div>
        );
    }
}
