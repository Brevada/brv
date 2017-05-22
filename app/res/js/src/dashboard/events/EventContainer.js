import React, { Component } from "react";
import PropTypes from "prop-types";
import _ from "lodash";

import Event from "dashboard/events/Event";
import Fetch from "forms/Fetch";
import { Filter } from "dashboard/aspects/Filter";

/**
 * Connects Event with data layer.
 *
 * @param   {object} props React props
 * @param   {object} event Event data
 * @param   {object} data  Fetched data
 * @returns {JSX}
 */
const FetchedEvent = props => {
    /* We merge new data with old data to allow selective updating. */
    const event = Object.assign({}, props.event, props.data);

    return (
        <Event
            {...props}

            aspects={event.aspects}
            title={event.title}
            from={event.from}
            summary={event.summary}
            completed={event.completed}
        />
    );
};

FetchedEvent.propTypes = {
    event: PropTypes.object,
    data: PropTypes.object
};

FetchedEvent.defaultProps = {
    event: {},
    data: {}
};

/**
 * Container for collection of individual events.
 */
export default class EventContainer extends Component {

    static propTypes = {
        events: PropTypes.arrayOf(PropTypes.object),
        storeId: PropTypes.number.isRequired,
        filter: PropTypes.string.isRequired
    };

    static defaultProps = {
        events: []
    };

    /**
     * @constructor
     * @param   {object} props React props
     */
    constructor(props) {
        super(props);

        this.state = {
            /* Maintain collection of events. */
            events: props.events || [],

            /* Blacklist of events that have been removed and thus shouldn't
             * be shown. */
            removed: [],

            /* ES6 Map to faciliate simplier and more efficient individual
             * event "reloading". */
            refreshes: new Map([])
        };

        this.remove = ::this.remove;
        this.refreshEvent = ::this.refreshEvent;
    }

    /**
     * @override
     */
    componentWillReceiveProps(nextProps) {
        /* Load events if new events are passed in. */

        if (!nextProps.events || _.isEqual(nextProps.events, this.state.events)) {
            return;
        }

        this.setState({
            events: nextProps.events
        });
    }

    /**
     * Add the "removed" event to the blacklist.
     *
     * @param   {number} id The event id that has been removed.
     * @returns {void}
     */
    remove(id) {
        this.setState(s => ({
            removed: s.removed.concat([id])
        }));
    }

    /**
     * Trigger a refresh for a particular event.
     *
     * @param   {number} id The event id that should be refreshed.
     * @returns {void}
     */
    refreshEvent(id) {
        this.setState(s => {
            const newMap = new Map(s.refreshes);

            newMap.set(id, (newMap.get(id) || 0) + 1);

            return {
                refreshes: newMap
            };
        });
    }

    /**
     * @override
     */
    render() {
        /* Sort events by completed status, and creation order (id). */
        return (
            <div>
                <div className="dl xlarge hint showing">
                    {"Showing all "}<span>{
                        this.state.events
                        .filter(a => !this.state.removed.includes(a.id))
                        .length || 0
                    }</span>{" Events"}
                </div>
                <div className="ly flex-v center-c-h event-container">
                    {this.state.events
                        .concat()
                        .filter(a => !this.state.removed.includes(a.id))
                        .sort((a, b) => ( // eslint-disable-line no-confusing-arrow
                            a.completed === b.completed ? // eslint-disable-line no-nested-ternary
                            b.id - a.id :
                            (a.completed === false ? -1 : // eslint-disable-line no-nested-ternary
                            (b.completed === false ? 1 : (
                                b.completed - a.completed
                            )))
                        ))
                        .map(event => (
                            <Fetch
                                action={`/api/event/${event.id}`}
                                data={{
                                    store: this.props.storeId,
                                    days: Filter.toDays(this.props.filter)
                                }}
                                refresh={this.state.refreshes.get(event.id) || -1}
                                key={event.id}>
                                <FetchedEvent
                                    key={event.id}
                                    id={event.id}
                                    event={event}
                                    filter={this.props.filter}
                                    storeId={this.props.storeId}
                                    onRemove={this.remove}
                                    onRefresh={() => ( // eslint-disable-line react/jsx-no-bind
                                        this.refreshEvent(event.id)
                                    )}
                                />
                            </Fetch>
                    ))}
                </div>
            </div>
        );
    }

}
