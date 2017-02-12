import React from 'react';

import { Filter } from 'dashboard/aspects/Filter';
import EventContainer from 'dashboard/events/EventContainer';
import TimeFilters from 'dashboard/TimeFilters';
import { DialogButtonActions } from 'dialogs/Dialog';
import NewEventDialog from 'dashboard/events/dialogs/NewEvent';
import Loader from 'dashboard/Loader';
import DataLayer from 'forms/DataLayer';

/**
 * Links event container with loader and data layer.
 *
 * @param {object} props
 * @param {boolean} props.loading Indicates whether data is still loading.
 * @param {number} props.storeId The id of the store.
 * @param {string} props.filter The time filter key from Filter.
 * @param {object} props.data Data from the data layer.
 */
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
                storeId={props.storeId}
            />
        );
    }
};

/**
 * Time filter toolbar for Events view.
 *
 * @param {object} props
 */
const EventTimeFilter = props => (
    <TimeFilters
        {...props}
        options={[
            { view: Filter.ensure('PAST_MONTH') },
            { view: Filter.ensure('PAST_6_MONTH') },
            { view: Filter.ensure('PAST_YEAR') },
            { view: Filter.ensure('ALL_TIME') }
        ]}
    />
);

/**
 * The main Events view.
 */
export default class EventsView extends React.Component {
    constructor() {
        super();

        this.state = {
            /* Currently selected filter. */
            filter: Filter.ensure('PAST_MONTH'),

            /* Visibility of dialogs. */
            dialogs: {
                'NEW_EVENT': false
            },

            /* Simpler enforcement of data layer refresh than checking multiple
             * properties for a change. */
            refresh: 0
        };

        this.onChangeFilter = ::this.onChangeFilter;
        this.newEventDialogAction = ::this.newEventDialogAction;
    }

    /**
     * Event handler for time filter change.
     */
    onChangeFilter(filter) {
        this.setState({ filter: filter });
    }

    /**
     * Event handler for new event dialog.
     */
    newEventDialogAction(action) {
        if (![DialogButtonActions.CLOSE,
              DialogButtonActions.OPEN,
              DialogButtonActions.SUCCESS].includes(action)) {
                  return;
              }

        this.setState(s => ({
            dialogs: Object.assign(s.dialogs, {
                'NEW_EVENT': action === DialogButtonActions.OPEN
            })
        }), () => {
            if (action === DialogButtonActions.SUCCESS) {
                this.setState(s => ({ refresh: s.refresh+1 }));
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
                <EventTimeFilter
                    onChange={this.onChangeFilter}
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
                        storeId={this.props.storeId}
                    />
                </DataLayer>
            </div>
        );
    }

}
