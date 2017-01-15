import React from 'react';
import ReactDOM from 'react-dom';

import { Link } from 'components/Link';
import { Filter } from 'dashboard/aspects/Filter';

import EventContainer from 'dashboard/events/EventContainer';
import TimeFilters from 'dashboard/TimeFilters';

import { DialogButtonActions } from 'dialogs/Dialog';
import NewEventDialog from 'dialogs/NewEventDialog';
import Loader from 'dashboard/Loader';

import DataLayer from 'forms/DataLayer';

const EventContainerLinked = props => {
    if (props.loading || !props.data) {
        return (
            <Loader
                messages={['Loading events...']}
            />
        );
    } else {
        return (
            <EventContainer
                filter={props.filter}
                events={props.data.events}
            />
        );
    }
};

export default class EventsView extends React.Component {
    constructor() {
        super();

        this.state = {
            filter: 'TODAY',
            dialogs: {
                'NEW_EVENT': false
            },
            refresh: 0
        };

        this.onChangeFilter = this.onChangeFilter.bind(this);
        this.newEventDialogAction = this.newEventDialogAction.bind(this);
    }

    onChangeFilter(filter) {
        this.setState({ filter: filter });
    }

    newEventDialogAction(action) {
        if (![DialogButtonActions.CLOSE,
              DialogButtonActions.OPEN,
              DialogButtonActions.SUCCESS].includes(action)) {
                  return;
              }

        this.setState({
            dialogs: Object.assign(this.state.dialogs, {
                'NEW_EVENT': action === DialogButtonActions.OPEN
            })
        }, () => {
            if (action === DialogButtonActions.SUCCESS) {
                this.setState({ refresh: this.state.refresh+1 });
            }
        });
    }

    render() {
        return (
            <div className='view'>
                {this.state.dialogs.NEW_EVENT && (
                    <NewEventDialog
                        storeId={this.props.storeId}
                        onAction={this.newEventDialogAction}
                    />
                )}
                <TimeFilters
                    onChangeFilter={this.onChangeFilter}
                    filter={this.state.filter}
                    actionLabel='+ Create a New Event'
                    onAction={() => {this.newEventDialogAction(DialogButtonActions.OPEN);}}
                />
                <DataLayer action="/api/events" data={{
                    store: this.props.storeId,
                    days: Filter.toDays(this.state.filter)
                }}
                refresh={this.state.refresh}>
                    <EventContainerLinked
                        filter={this.state.filter}
                    />
                </DataLayer>
            </div>
        );
    }

}
