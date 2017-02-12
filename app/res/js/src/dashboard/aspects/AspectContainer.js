import React from 'react';
import _ from 'lodash';

import Aspect from 'dashboard/aspects/Aspect';

/**
 * Container for individual aspects.
 */
export default class AspectContainer extends React.Component {
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
     * @param {number} The id of the aspect that has been removed.
     */
    onRemove(id) {
        this.setState(s => ({
            removed: s.removed.concat([id])
        }));
    }

    render() {
        /* Sort aspects by title, and remove blacklisted aspects
         * (removed list). */
        return (
            <div className='ly flex-h center-c-h aspect-container'>
                {this.state.aspects
                    .concat()
                    .filter(a => !this.state.removed.includes(a.id))
                    .sort((a,b) => a.title.localeCompare(b.title))
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
