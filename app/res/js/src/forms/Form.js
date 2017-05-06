import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import setStatePromise from 'utils/StatePromise';
import classNames from 'classnames';
import getFormData from 'get-form-data';
import axios from 'axios';

import Input from 'forms/Input';
import Textarea from 'forms/inputs/Textarea';
import Textbox from 'forms/inputs/Textbox';

/**
 * API "upload" layer. Emulates a DOM form.
 */
export default class Form extends Component {

    static defaultProps = {
        once: true,
        method: 'post',
        center: false,
        inline: false
    };

    static propTypes = {
        /* Whether to restrict submissions to one at a time. */
        once: PropTypes.bool,

        /* The API endpoint. */
        action: PropTypes.string.isRequired,

        /* HTTP method to use in sending the data. */
        method: PropTypes.string,

        /* Data to pass to server endpoint. This is concatenated with any
         * form data. */
        data: PropTypes.object,

        /* Success and Error callbacks. */
        onSuccess: PropTypes.func,
        onError: PropTypes.func,

        /* Whether to apply inline styling. */
        inline: PropTypes.bool,

        /* Whether to apply center styling. */
        center: PropTypes.bool,

        /* Reference callback for the form instance. Similar to ref. */
        form: PropTypes.func
    };

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

    /**
     * Submit handler for forms. Packages form data and submits the data
     * to the server (or local interceptor).
     *
     * @param  {Event} e
     */
    submit(e) {
        if (e) e.preventDefault();
        if (!this._form) return;

        /* Skip if already submitting and can only submit one at a time. */
        if (this.props.once !== false && this.state.submitting) return;

        /* Use DOM to retrieve form data. This allows retrieval from deeply
         * nested elements. */
        let formData = getFormData(ReactDOM.findDOMNode(this._form));

        let method = (this.props.method || 'post').trim().toLowerCase();

        let data = Object.assign({}, formData || {}, this.props.data),
            dataKey = ['put', 'post', 'patch'].includes(method) ?
                      'data' :
                      'params';

        data = { [dataKey]: data };

        /* If defined, use interceptor to allow offline functionality in offline modes. */
        const ajax = (window.brv && window.brv.feedback) ?
                     window.brv.feedback.interceptor || axios :
                     axios;

        setStatePromise.call(this, {
            submitting: true
        })
        .then(() => (
            ajax(Object.assign({
                method: method,
                url: this.props.action
            }, data))
        ))
        .then(response => {
            if (this.props.onSuccess) this.props.onSuccess(response);
        })
        .catch(error => {
            if (this.props.onError) this.props.onError(error.response || error);
        })
        .then(() => {
            if (!this._unmounted) this.setState({ submitting: false });
        });
    }

    render() {
        /* Pass instance of this form to all its children. */
        return (
            <div className={classNames('form', {
                submitting: this.state.submitting,
                inline: !!this.props.inline,
                center: !!this.props.center
            }, this.props.className || '')}>
                <form
                    onSubmit={this.submit}
                    ref={frm => {
                        this._form = frm;
                        this.props.form && this.props.form(this);
                    }}>
                    {React.Children.map(this.props.children, (child) => {
                        if (child && child.type.prototype instanceof React.Component) {
                            return React.cloneElement(child, {
                                form: () => this._form && this
                            });
                        }

                        return child;
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
class Group extends Component {

    static propTypes = {
        form: PropTypes.func
    };

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
        if (this._input) ReactDOM.findDOMNode(this._input).focus();
    }

    /**
     * Save the reference to the input. Assumes single input.
     */
    inputCallback(input){
        this._input = input;
    }

    render() {
        /* Pass form to its children and save the input reference. */
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
                             });
                         } else if (child.type.prototype instanceof React.Component) {
                             return React.cloneElement(child, {
                                 form: this.props.form
                             });
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
 * @param {object} props
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
 * Generic error message holder to be used by a Form.
 */
const ErrorMessage = props => (
    <div className='form-error'>{props.text}</div>
);

export {
    Input,
    Group,
    Label,
    Textbox,
    Textarea,
    ErrorMessage
};
