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
        e.preventDefault();

        // Skip if already submitting.
        if (this.props.once !== false && this.state.submitting) return;

        let formData = getFormData(e.target);

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
                submitting: this.state.submitting
            }, this.props.className || '')}>
                <form onSubmit={this.submit}>
                {this.props.children}
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
                     if (child.type === Label) {
                         return React.cloneElement(child, {
                             onClick: this.labelClicked
                         });
                     } else if (child.type.prototype instanceof Input) {
                         return React.cloneElement(child, {
                             ref: this.inputCallback
                         })
                     } else {
                         return child;
                     }
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

const ErrorMessage = props => (
    <div className='form-error'>{props.text}</div>
);

export { Input, Group, Label, Textbox, Button, ErrorMessage };
