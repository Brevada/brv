/* global brv */

import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";
import Form from "forms/Form";
import stateQueue from "utils/StateQueue";

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

            /* Indicates that feedback for this aspect has already been
             * submitted. */
            submitted: false,

            /* Indicates that feedback is being submitted. */
            submitting: false,

            /* Indicates that the aspect is in the process of being removed. */
            removing: false,

            /* Rating data. */
            value: null,
            ordinal: null,
            value_type: null
        };

        this.form = null;

        this.onSubmit = ::this.onSubmit;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
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

            /* Play out animation. */
            stateQueue(this, () => !this._unmounted)
                .do({ submitting: true })
                .wait(250)
                .do({
                    submitted: true,
                    submitting: false
                })
                .wait(700)
                .do({
                    removing: true
                })
                .wait(300)
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

        const ResponseInput = this.props.valueTypes === null ?
            RatingBarInput :
            MultiOptionInput;

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
                    className={classNames("item", "aspect", {
                        submitting: this.state.submitting && !this.state.submitted,
                        removing: this.state.removing
                    })}>
                    <div className="header">{this.props.title}</div>
                    {(this.state.submitted && (<Submitted />)) || (
                        <ResponseInput
                            onSubmit={this.onSubmit}
                            valueTypes={this.props.valueTypes}
                        />
                    )}
                </div>
            </Form>
        );
    }
}
