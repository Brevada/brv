import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';

import getFormData from 'get-form-data';
import axios from 'axios';

export default class Form extends React.Component {
    constructor() {
        super();

        this.state = {
            submitting: false
        };

        this.submit = this.submit.bind(this);
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    submit(e) {
        if (e) e.preventDefault();
        if (!this._form) return;

        // Skip if already submitting.
        if (this.props.once !== false && this.state.submitting) return;

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

class Group extends React.Component {
    constructor(){
        super();

        this.labelClicked = this.labelClicked.bind(this);
        this.inputCallback = this.inputCallback.bind(this);
    }

    labelClicked() {
        if (this._input) {
            ReactDOM.findDOMNode(this._input).focus();
        }
    }

    inputCallback(input){
        this._input = input;
    }

    render() {
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

const Label = props => (
    <label
        className={classNames('label', {
            inline: props.inline === true
        })}
        onClick={props.onClick || (()=>(false))}>{props.text}</label>
);

class Input extends React.Component {
    constructor(props) {
        super(props);
    }
}

class Textbox extends Input {
    render() {
        let passedProps = this.props.props || {};
        return (
            <div className='input' tabIndex={0} onFocus={()=>{
                this._input && ReactDOM.findDOMNode(this._input).focus();
            }}>
                <input
                    className={'textbox'}
                    name={this.props.name}
                    placeholder={this.props.placeHolder || ''}
                    type={this.props.type || 'text'}
                    ref={ input => (this._input = input)}
                    {...passedProps}
                />
            </div>
        );
    }
}

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

const ErrorMessage = props => (
    <div className='form-error'>{props.text}</div>
);

export { Input, Group, Label, Textbox, Button, Link, ErrorMessage };
