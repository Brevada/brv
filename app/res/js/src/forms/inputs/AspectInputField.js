import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";
import _ from "lodash";

import { Input } from "forms/Form";

/**
 * Single aspect option.
 * @param   {object} props React props
 * @param   {function} props.onClick On click event handler.
 * @param   {string} props.className Optional class.
 * @param   {string} props.label Display text.
 * @returns {JSX}
 */
const AspectOption = props => (
    <div
        className={classNames("option", props.className)}
        onMouseDown={props.onClick}>
        {props.label}
    </div>
);

AspectOption.propTypes = {
    className: PropTypes.string,
    onClick: PropTypes.func,
    label: PropTypes.string.isRequired
};

AspectOption.defaultProps = {
    className: "",
    onClick: () => { /* no op */ }
};

/**
 * Aspect options drop down.
 * @param {object} props React props.
 *
 * @returns {JSX}
 */
const AspectOptions = props => {

    /**
     * Handles custom option selected event.
     * @returns {void}
     */
    const onCustomSelected = () => {
        props.onSelect({
            id: -1,
            title: props.value.trim()
        });
    };

    const showCustom = props.custom && props.id === -1 && props.value.length > 0;
    const customOption = showCustom ? (
        <AspectOption
            label={`"${props.value.trim()}"`}
            className="new"
            onClick={onCustomSelected}
        />
     ) : null;

    return (
        <div className="aspect-options">
            {customOption}

            { props.types.filter(type =>
                props.value.length === 0 ||
                type.title.toLowerCase().indexOf(props.value.toLowerCase()) >= 0
            ).map(type => {
                // eslint-disable-next-line require-jsdoc
                const onSelect = () => {
                    props.onSelect(type);
                };

                return (
                    <AspectOption
                        key={type.id}
                        label={type.title}
                        onClick={onSelect}
                    />
                );
            }) }
        </div>
    );
};

AspectOptions.propTypes = {
    custom: PropTypes.bool,
    value: PropTypes.string.isRequired,
    id: PropTypes.number.isRequired,
    types: PropTypes.arrayOf(PropTypes.object).isRequired
};

AspectOptions.defaultProps = {
    custom: false
};

/**
 * Form input element to provide selection of aspect types.
 */
export default class AspectInputField extends Input {

    static contextTypes = {
        form: PropTypes.func
    };

    static propTypes = {
        types: PropTypes.arrayOf(PropTypes.object).isRequired,
        submitOnSelect: PropTypes.bool,
        name: PropTypes.string.isRequired,
        placeHolder: PropTypes.string
    };

    static defaultProps = {
        submitOnSelect: false,
        placeHolder: ""
    };

    /**
     * @constructor
     * @param  {object} props React props
     */
    constructor(props) {
        super(props);

        this.state = {

            /* Value of aspect type (label). */
            value: "",

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
        this.saveRef = ::this.saveRef;
    }

    /**
     * @override
     */
    componentWillReceiveProps(nextProps) {
        /* Update state if aspect types differ. */

        if (!nextProps.types || _.isEqual(nextProps.types, this.state.types)) {
            return;
        }

        this.setState({
            types: nextProps.types
        });
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * If aspect item is selected, and submitOnSelect is true, then allow
     * submission of the form.
     * @returns {void}
     */
    potentialSubmit() {
        if (this._inputText && this.props.submitOnSelect && this.context.form()) {
            this.context.form().submit();
            if (!this._unmounted) {
                this.setState({
                    value: "",
                    id: -1,
                    show: false
                });
            }
        }
    }

    /**
     * onKeyPress event handler. Support form submission on enter.
     * @param   {Event} e Key press event.
     * @returns {void}
     */
    onKeyPress(e) {
        if (e.key == "Enter") {
            this.potentialSubmit();
        }
    }

    /**
     * Event handler for aspect selection.
     * @param   {object} type The aspect type selected.
     * @returns {void}
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
     * @param   {Event} e The input onChange event.
     * @returns {void}
     */
    onTextChange(e) {
        if (this._unmounted) return;

        let id = -1;

        if (this.state.value.length > 0) {
            const types = this.state.types.filter(type => (
                type.title.toLowerCase() == this.state.value.trim().toLowerCase()
            ));

            if (types.length > 0) {
                id = types[0].id;
            }
        }

        this.setState({
            value: e.target.value,
            id
        });
    }

    /**
     * On focus, redirect focus to textbox and show drop down.
     * @param   {Event} e The input onFocus event.
     * @returns {void}
     */
    onFocus() {
        if (this._unmounted || this.state.show) return;

        this.setState({
            show: true
        }, () => {
            this._inputText && this._inputText.focus();
        });
    }

    /**
     * Blur event handler. Hide drop down.
     * @param   {Event} e The input onBlur event.
     * @returns {void}
     */
    onBlur() {
        if (this._unmounted || !this.state.show) return;

        this.setState({
            show: false
        });
    }

    /**
     * Saves reference to input.
     * @param   {DOMElement} input Input to save.
     * @returns {void}
     */
    saveRef(input) {
        this._inputText = input;
    }

    /**
     * @override
     */
    render() {
        return (
            <div
                className="aspect-input"
                tabIndex={0}
                onFocus={this.onFocus}>
                <input
                    className="textbox"
                    name={this.props.name}
                    placeholder=""
                    type="text"
                    ref={this.saveRef}
                    onChange={this.onTextChange}
                    value={this.state.value}
                    onFocus={this.onFocus}
                    onBlur={this.onBlur}
                    onKeyPress={this.onKeyPress}
                    autoComplete="off"
                />
                { this.state.value.length === 0 &&
                    <div className="placeholder">{ this.props.placeHolder || "" }</div>
                 }
                <input
                    type="hidden"
                    value={this.state.id}
                    name={`${this.props.name}_id`}
                />
                { this.state.show &&
                    <AspectOptions
                        id={this.state.id}
                        custom={this.props.custom}
                        value={this.state.value}
                        types={this.state.types}
                        onSelect={this.onSelect}
                    />
                 }
            </div>
        );
    }
}
