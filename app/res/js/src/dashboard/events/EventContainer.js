import React from 'react';
import ReactDOM from 'react-dom';
import equal from 'deep-equal';

import Event from 'dashboard/events/Event';

export default class EventContainer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            events: props.events || [],
            removed: []
        };

        this.remove = this.remove.bind(this);
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
                            <Event
                                key={event.id}
                                id={event.id}
                                title={event.title}
                                from={event.from}
                                summary={event.summary}
                                aspects={event.aspects}
                                completed={event.completed}
                                filter={this.props.filter}
                                storeId={this.props.storeId}
                                onRemove={this.remove}
                            />
                    ))}
                </div>
            </div>
        );
    }

}
