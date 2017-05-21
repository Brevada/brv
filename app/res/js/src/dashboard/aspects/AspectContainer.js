import React, { Component } from "react";
import PropTypes from "prop-types";
import _ from "lodash";

import Aspect from "dashboard/aspects/Aspect";

/**
 * Container for individual aspects.
 */
export default class AspectContainer extends Component {

    static propTypes = {
        aspects: PropTypes.arrayOf(PropTypes.object),
        filter: PropTypes.string.isRequired
    };

    static defaultProps = {
        aspects: []
    };

    /**
     * @constructor
     * @param   {object} props React props
     */
    constructor(props) {
        super(props);

        this.state = {
            aspects: props.aspects || [],

            /* Maintain a list of removed elements to show removal immediately,
             * rather than wait for API response. */
            removed: []
        };

        this.onRemove = ::this.onRemove;
    }

    /**
     * @override
     */
    componentWillReceiveProps(nextProps) {
        if (!nextProps.aspects || _.isEqual(nextProps.aspects, this.state.aspects)) {
            return;
        }

        this.setState({
            aspects: nextProps.aspects
        });
    }

    /**
     * Handles remove event from an aspect. "Removing" the aspect from the
     * displayed list (via a blacklist).
     *
     * @param   {number} id The id of the aspect that has been removed.
     * @returns {void}
     */
    onRemove(id) {
        this.setState(s => ({
            removed: s.removed.concat([id])
        }));
    }

    /**
     * @override
     */
    render() {
        /* Sort aspects by title, and remove blacklisted aspects
         * (removed list). */
        return (
            <div className="ly flex-h aspect-container">
                {this.state.aspects
                    .concat()
                    .filter(a => !this.state.removed.includes(a.id))
                    .sort((a, b) => a.title.localeCompare(b.title))
                    .map(aspect => (
                        <Aspect
                            key={aspect.id}
                            id={aspect.id}
                            title={aspect.title}
                            summary={aspect.summary}
                            filter={this.props.filter}
                            onRemove={this.onRemove}
                        />
                ))}
            </div>
        );
    }

}
