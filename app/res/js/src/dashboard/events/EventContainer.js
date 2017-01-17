import React from 'react';
import ReactDOM from 'react-dom';
import equal from 'deep-equal';
import omit from 'lodash.omit';

import Event from 'dashboard/events/Event';
import DataLayer from 'forms/DataLayer';
import { Filter } from 'dashboard/aspects/Filter';

const EventLinked = props => {
    let p = omit(props, ['data', 'aspects']);
    let event = Object.assign({}, props.event, props.data);
    return (
        <Event
            {...p}
            aspects={event.aspects}
            title={event.title}
            from={event.from}
            summary={event.summary}
            aspects={event.aspects}
            completed={event.completed}
        />
    );
};

export default class EventContainer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            events: props.events || [],
            removed: [],
            refreshes: new Map([])
        };

        this.remove = this.remove.bind(this);
        this.refreshEvent = this.refreshEvent.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        if (!nextProps.events || equal(nextProps.events, this.state.events)) {
            return;
        }

        this.setState({
            events: nextProps.events
        });
    }

    remove(id) {
        this.setState({
            removed: this.state.removed.concat([id])
        });
    }

    refreshEvent(id) {
        let newMap = new Map(this.state.refreshes);
        newMap.set(id, (newMap.get(id) || 0) + 1);
        this.setState({
            refreshes: newMap
        });
    }

    render() {
        return (
            <div>
                <div className='dl xlarge hint showing'>
                    Showing all <span>{
                        this.state.events
                        .filter(a => !this.state.removed.includes(a.id))
                        .length || 0
                    }</span> Events
                </div>
                <div className='ly flex-v center-c-h event-container'>
                    {this.state.events
                        .concat()
                        .filter(a => !this.state.removed.includes(a.id))
                        .sort((a,b) => (
                            a.completed === b.completed ?
                            b.id - a.id :
                            (a.completed === false ? -1 :
                            (b.completed === false ? 1 : (
                                b.completed - a.completed
                            )))
                        ))
                        .map(event => (
                            <DataLayer action={`/api/event/${event.id}`} data={{
                                store: this.props.storeId,
                                days: Filter.toDays(this.props.filter)
                            }} refresh={this.state.refreshes.get(event.id) || -1} key={event.id}>
                            <EventLinked
                                key={event.id}
                                id={event.id}
                                event={event}
                                filter={this.props.filter}
                                storeId={this.props.storeId}
                                onRemove={this.remove}
                                onRefresh={()=>this.refreshEvent(event.id)}
                            />
                            </DataLayer>
                    ))}
                </div>
            </div>
        );
    }

}
