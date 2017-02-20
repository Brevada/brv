import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import getFormData from 'get-form-data';
import axios from 'axios';

/**
 * API "upload" layer. Emulates a DOM form.
 */
export default class Form extends React.Component {
    constructor() {
        super();

        this.state = {
            /* Indicates "submitting" status (loading). */
            submitting: false
        };

        this.submit = ::this.submit;
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    submit(e) {
        if (e) e.preventDefault();
        if (!this._form) return;

        /* Skip if already submitting and can only submit one at a time. */
        if (this.props.once !== false && this.state.submitting) return;

        /* Use DOM to retrieve form data. This allows retrieval from deeply
         * nested elements. */
        let formData = getFormData(ReactDOM.findDOMNode(this._form));

        this.setState({
            submitting: true
        }, () => {
            let method = (this.props.method || 'post').trim().toLowerCase();

            let data = Object.assign({}, formData || {}, this.props.data);
            if (['put', 'post', 'patch'].includes(method)) {
                data = { data: data };
            } else {
                data = { params: data };
            }

            axios(Object.assign({
                method: method,
                url: this.props.action
            }, data))
            .then(response => {
                if (this.props.onSuccess) {
                    this.props.onSuccess(response);
                }
            })
            .catch(error => {
                if (this.props.onError) {
                    this.props.onError(error.response || error);
                }
            })
            .then(() => {
                if (!this._unmounted) {
                    this.setState({ submitting: false });
                }
            });
        });
    }

    render() {
        /* Pass instance of this form to all its children. */
        return (
            <div className={classNames('form', {
                submitting: this.state.submitting,
                inline: this.props.inline === true
            }, this.props.className || '')}>
                <form onSubmit={this.submit} ref={frm=>this._form=frm}>
                    {React.Children.map(this.props.children,
                     (child) => {
                         if (child && child.type.prototype instanceof React.Component) {
                             return React.cloneElement(child, {
                                 form: () => {
                                     if(this._form){
                                         return this;
                                     }
                                 }
                             })
                         } else {
                             return child;
                         }
                     })}
                </form>
            </div>
        );
    }
}

/**
 * Groups form elements within a form. Supports pairing an
 * element with a label.
 */
class Group extends React.Component {
    constructor(){
        super();

        this.labelClicked = ::this.labelClicked;
        this.inputCallback = ::this.inputCallback;
    }

    /**
     * If a label is clicked, and there is an input element, give focus
     * to the input.
     */
    labelClicked() {
        if (this._input) {
            ReactDOM.findDOMNode(this._input).focus();
        }
    }

    /**
     * Save the reference to the input. Assumes single input.
     */
    inputCallback(input){
        this._input = input;
    }

    render() {
        /* Pass form to its children and save the input reference.*/
        return (
            <div className={'form-group ' + (this.props.className || '')}>
                {React.Children.map(this.props.children,
                 (child) => {
                     if (child) {
                         if (child.type === Label) {
                             return React.cloneElement(child, {
                                 onClick: this.labelClicked
                             });
                         } else if (child.type.prototype instanceof Input) {
                             return React.cloneElement(child, {
                                 ref: this.inputCallback,
                                 form: this.props.form
                             })
                         } else if (child.type.prototype instanceof React.Component) {
                             return React.cloneElement(child, {
                                 form: this.props.form
                             })
                         }
                     }
                     return child;
                 })}
            </div>
        );
    }
}

/**
 * A form label.
 * @param {string} props.text The display text.
 * @param {boolean} props.inline Set display style to inline.
 * @param {function} props.onClick On click event handler.
 */
const Label = props => (
    <label
        className={classNames('label', {
            inline: props.inline === true
        })}
        onClick={props.onClick || (()=>(false))}>
        {props.text}
    </label>
);

/**
 * Abstract Input class. Used for polymorphism.
 */
class Input extends React.Component {
    constructor(props) {
        super(props);
    }
}

/**
 * A basic textbox.
 */
class Textbox extends Input {
    constructor(props) {
        super(props);

        this.state = {
            /* The textbox value. */
            value: ''
        };

        this.onFocus = ::this.onFocus;
        this.onChange = ::this.onChange;
    }

    onFocus() {
        /* On focus, direct focus to nested DOM element. */
        this._input && ReactDOM.findDOMNode(this._input).focus();
    }

    onChange(e) {
        /* Save the textbox's value to the state. */
        this.setState({
            value: e.target.value || ''
        });
    }

    render() {
        let passedProps = this.props.props || {};
        return (
            <div className='input' tabIndex={0} onFocus={this.onFocus}>
                <input
                    className={classNames('textbox', {
                        'seamless': this.props.seamless === true
                    })}
                    name={this.props.name}
                    type={this.props.type || 'text'}
                    onChange={this.onChange}
                    ref={ input => (this._input = input)}
                    {...passedProps}
                />
                { this.state.value.length === 0 && (
                    <div className='placeholder'>{ this.props.placeHolder || '' }</div>
                ) }
            </div>
        );
    }
}

/**
 * Simple button.
 */
const Button = props => (
    <button
        className={classNames(props.className || '', 'btn', {
            'submit': props.submit === true,
            'right': props.right === true,
            'left': props.left === true
        })}
        type={props.submit === true ? 'submit' : 'button'}
        onClick={() => {
            if (props.onClick) props.onClick();
        }}
    >{props.label}</button>
);

/**
 * Simple link.
 */
const Link = props => (
    <button
        className={classNames(props.className || '', 'link', {
            'submit': props.submit === true,
            'right': props.right === true,
            'left': props.left === true,
            'danger': props.danger === true
        })}
        type={props.submit === true ? 'submit' : 'button'}
        onClick={() => {
            if (props.onClick) props.onClick();
        }}
    >{props.label}</button>
);

/**
 * Generic error message holder to be used by a Form.
 */
const ErrorMessage = props => (
    <div className='form-error'>{props.text}</div>
);

export { Input, Group, Label, Textbox, Button, Link, ErrorMessage };
