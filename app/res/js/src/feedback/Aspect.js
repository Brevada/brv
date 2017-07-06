/* global brv */

import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";
import Form from "forms/Form";
import stateQueue from "utils/StateQueue";

import velocity from "velocity-animate";

import ResponseInput from "feedback/inputs/ResponseInput";
import RatingBarInput from "feedback/inputs/Rating";
import MultiOptionInput from "feedback/inputs/MultiOption";

/**
 * A message to display when a user has given feedback for an aspect.
 * @returns {JSX}
 */
const Submitted = () => (
    <div className="submitted">
        {"Thank you for giving feedback."}
    </div>
);

/**
 * Individual aspect.
 */
export default class Aspect extends React.Component {

    static propTypes = {
        id: PropTypes.number.isRequired,
        title: PropTypes.string.isRequired,
        valueTypes: PropTypes.object,
        onSubmit: PropTypes.func,
        session: PropTypes.string.isRequired
    };

    static defaultProps = {
        onSubmit: () => { /* no op */ },
        valueTypes: null
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {

            /* Indicates that feedback for this aspect has already been submitted. */
            submitted: false,

            /* Rating data. */
            value: null,
            ordinal: null,
            value_type: null
        };

        this.form = null;

        this.onSubmit = ::this.onSubmit;
        this.setContainer = ::this.setContainer;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Handles transition event when element is being removed from DOM.
     * @param  {Function} cb callback to invoke when animation complete
     * @returns {void}
     */
    componentWillLeave(cb) {
        velocity(this.container, "slideUp", { duration: 300 }).then(cb);
    }

    /**
     * Binds the input's outer div to this instance.
     * @param {object} c The div reference.
     * @returns {void}
     */
    setContainer(c) {
        this.container = c;
    }

    /**
     * Man in the Middle. When a rating is given, an animation can play
     * before the item is removed from the list.
     *
     * @param   {number} value The rating's value.
     * @param   {number} ordinal The rating's ranking or ordinal.
     * @param   {string} value_type The value type, in the case of multi-option.
     * @returns {void}
     */
    onSubmit({ value, ordinal, value_type }) { // eslint-disable-line complexity
        if (this.state.submitted || this.state.submitting) return;

        this.setState({
            value: value == null ? null : parseFloat(value),
            ordinal: ordinal == null ? null : parseInt(ordinal),
            value_type
        }, () => {

            /* Submit data. */
            this.form && this.form.submit();

            stateQueue(this, () => !this._unmounted)
                .do({ submitted: true })
                .wait(700)
                .do({ removed: true })
                .do(() => {
                    brv.feedback.session.onSubmit({ value, ordinal, value_type });
                    this.props.onSubmit(this.props.id);
                })
                .exec();
        });
    }

    /**
     * @override
     */
    render() {
        const saveFormRef = f => ( // eslint-disable-line require-jsdoc
            this.form = f
        );

        const InputType = this.props.valueTypes === null ? RatingBarInput : MultiOptionInput;

        return (
            <Form
                method="POST"
                action="/api/feedback/response"
                data={{
                    store: brv.feedback.id() || false,
                    session: this.props.session,
                    aspect_id: this.props.id,
                    value: this.state.value,
                    ordinal: this.state.ordinal,
                    value_type: this.state.value_type
                }}
                form={saveFormRef}>
                <div
                    className={classNames("item", "aspect")}
                    ref={this.setContainer}>
                    <div className="header">{this.props.title}</div>
                    {(!this.state.submitted && (
                        <ResponseInput
                            input={InputType}
                            onSubmit={this.onSubmit}
                            valueTypes={this.props.valueTypes}
                        />
                    )) || (
                        <Submitted />
                    )}
                </div>
            </Form>
        );
    }
}
