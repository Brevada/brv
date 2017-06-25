import React, { Component } from "react";
import PropTypes from "prop-types";

import Label from "forms/Label";
import AbstractTextInput from "forms/AbstractTextInput";

/**
 * Groups form elements within a form. Supports pairing an
 * element with a label.
 */
export default class Group extends Component {

    static propTypes = {
        className: PropTypes.string,
        children: PropTypes.node.isRequired
    };

    static defaultProps = {
        className: ""
    };

    /**
     * @constructor
     */
    constructor(){
        super();

        this.labelClicked = ::this.labelClicked;
        this.inputCallback = ::this.inputCallback;
    }

    /**
     * If a label is clicked, and there is an input element, give focus
     * to the input.
     * @returns {void}
     */
    labelClicked() {
        if (this._input) this._input.onFocus();
    }

    /**
     * Save the reference to the input. Assumes single input.
     * @param   {Input} input Input to save.
     * @returns {void}
     */
    inputCallback(input){
        this._input = input;
    }

    /**
     * @override
     */
    render() {

        /* Save the input reference and label if exists. */
        return (
            <div className={`form-group ${this.props.className}`}>
                {React.Children.map(this.props.children,
                 (child) => {
                     if (child) {
                         if (child.type === Label) {
                             return React.cloneElement(child, {
                                 onClick: this.labelClicked
                             });
                         } else if (child.type.prototype instanceof AbstractTextInput) {
                             return React.cloneElement(child, {
                                 ref: this.inputCallback
                             });
                         }
                     }

                     return child;
                 })}
            </div>
        );
    }
}
