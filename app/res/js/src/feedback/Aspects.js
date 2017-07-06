/* global brv */

import React from "react";
import PropTypes from "prop-types";
import _ from "lodash";
import TransitionGroup from "react-transition-group/TransitionGroup";

import Aspect from "feedback/Aspect";

/**
 * Scrollable list of aspects to give feedback on.
 */
export default class Aspects extends React.Component {

    static propTypes = {
        aspects: PropTypes.array.isRequired,
        onSubmit: PropTypes.func,
        session: PropTypes.string.isRequired
    };

    static defaultProps = {
        onSubmit: () => { /* no op */ }
    };

    /**
     * @constructor
     * @param  {object} props React props
     */
    constructor(props) {
        super(props);

        this.state = {
            aspects: _.shuffle(props.aspects),

            /* Maintain list of "submitted" aspects. */
            submitted: []
        };

        this.onSubmit = ::this.onSubmit;
    }

    /**
     * @override
     * @param  {object} nextProps React props
     */
    componentWillReceiveProps(nextProps) {
        if (!nextProps.aspects || _.isEqual(
            _.sortBy(nextProps.aspects, ["id"]),
            _.sortBy(this.state.aspects, ["id"]))) {
            return;
        }

        this.setState({
            aspects: _.shuffle(nextProps.aspects)
        });
    }

    /**
     * Handles submit event from an aspect. "Submitting" the aspect, removes
     * it from the displayed list (via a blacklist).
     *
     * @param   {number} id The id of the aspect that has been submitted.
     * @returns {void}
     */
    onSubmit(id) {
        this.setState(s => ({
            submitted: s.submitted.concat([id])
        }), this.props.onSubmit);
    }

    /**
     * @override
     */
    render() {
        /* Don't show blacklisted aspects (removed list). */
        const aspects = this.state.aspects
            .concat()
            .filter(a => (
                !this.state.submitted.includes(a.id)
            ));

        if (brv.feedback) {
            brv.feedback.session.setRemainingCount(aspects.length);
        }

        return (
            <div className="ly keep-spacing flex-v center-c-h aspect-container">
                <TransitionGroup>
                    {aspects.map(aspect => (
                        <Aspect
                            key={aspect.id}
                            id={aspect.id}
                            title={aspect.title}
                            valueTypes={aspect.value_types}
                            onSubmit={this.onSubmit}
                            session={this.props.session}
                        />
                    ))}
                </TransitionGroup>
            </div>
        );
    }

}
