import React, { Component } from "react";
import PropTypes from "prop-types";

import setStatePromise from "utils/StatePromise";
import classNames from "classnames";
import getFormData from "get-form-data";
import ajax from "utils/Ajax";

import Input from "forms/Input";
import Textarea from "forms/inputs/Textarea";
import Textbox from "forms/inputs/Textbox";
import Label from "forms/Label";
import Group from "forms/Group";
import ErrorMessage from "forms/ErrorMessage";

/**
 * API "upload" layer. Emulates a DOM form.
 */
export default class Form extends Component {

    static defaultProps = {
        once: true,
        method: "post",
        center: false,
        inline: false,
        data: {},
        onSuccess: () => { /* no op */ },
        onBegin: () => { /* no op */ },
        onError: () => { /* no op */ },
        form: () => { /* no op */ },
        children: null,
        className: ""
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
        form: PropTypes.func,

        /* Callback called when form submission begins. Use onSuccess and
         * and onFailure to handle complete. */
        onBegin: PropTypes.func,

        /* Form elements. */
        children: PropTypes.node,

        /* Optional form class name. */
        className: PropTypes.string
    };

    static childContextTypes = {
        form: PropTypes.func
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {

            /* Indicates "submitting" status (loading). */
            submitting: false
        };

        this.submit = ::this.submit;
        this.saveFormRef = ::this.saveFormRef;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * @override
     */
    getChildContext() {
        return {
            form: () => this._form && this
        };
    }

    /**
     * Returns form's method and wrapped data for use with ajax.
     * @returns {object}
     */
    getFormState() {
        /* Use DOM to retrieve form data. This allows retrieval from deeply
         * nested elements. */
        const formData = getFormData(this._form),
            method = (this.props.method || "post").trim().toLowerCase();

        const dataKey = ["put", "post", "patch"].includes(method) ?
                        "data" :
                        "params";

        const data = { [dataKey]: (
            Object.assign({}, formData || {}, this.props.data)
        ) };

        return {
            method,
            data
        };
    }

    /**
     * Submit handler for forms. Packages form data and submits the data
     * to the server (or local interceptor).
     *
     * @param   {Event} e Form event.
     * @returns {void}
     */
    submit(e) {
        e && e.preventDefault();

        /* Skip if form is not defined, or already submitting and can only
         * submit one at a time. */
        const ready = this._form && (!this.props.once || !this.state.submitting);

        if (!ready) return;

        const {
            method,
            data
        } = this.getFormState();

        this.props.onBegin && this.props.onBegin();

        setStatePromise.call(this, {
            submitting: true
        })
        .then(() =>
            ajax(Object.assign({
                method,
                url: this.props.action
            }, data))
        )
        .then(response => this.props.onSuccess && this.props.onSuccess(response))
        .catch(error => this.props.onError && this.props.onError(error.response || error))
        .then(() => {
            if (!this._unmounted) this.setState({ submitting: false });
        });
    }

    /**
     * Saves form reference.
     * @param   {DOMElement} frm The form's DOM object.
     * @returns {void}
     */
    saveFormRef(frm) {
        this._form = frm;
        this.props.form && this.props.form(this);
    }

    /**
     * @override
     */
    render() {
        const formClasses = classNames("form", {
            submitting: this.state.submitting,
            inline: Boolean(this.props.inline),
            center: Boolean(this.props.center)
        }, this.props.className || "");

        /* Pass instance of this form to all its children. */
        return (
            <div
                className={formClasses}>
                <form
                    onSubmit={this.submit}
                    ref={this.saveFormRef}>
                    {this.props.children}
                </form>
            </div>
        );
    }
}

export {
    Input,
    Group,
    Label,
    Textbox,
    Textarea,
    ErrorMessage
};
