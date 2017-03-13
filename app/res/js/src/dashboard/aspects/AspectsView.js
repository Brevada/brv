import React from 'react';

import { Filter } from 'dashboard/aspects/Filter';

import AspectContainer from 'dashboard/aspects/AspectContainer';
import TimeFilters from 'dashboard/TimeFilters';

import { DialogButtonActions } from 'dialogs/Dialog';
import NewAspectDialog from 'dashboard/aspects/dialogs/NewAspect';
import Loader from 'dashboard/Loader';

import DataLayer from 'forms/DataLayer';

/**
 * Aspect container combined with a loading state.
 *
 * @param {object} props
 * @param {boolean} props.loading Whether the aspect container is loading data.
 * @param {string} props.filter The time filter key from Filter.
 * @param {object} props.data Data from the data layer.
 */
const AspectContainerLinked = props => {
    if (props.loading || !props.data) {
        return (
            <Loader
                messages={['Loading aspects...']}
            />
        );
    } else {
        return (
            <AspectContainer
                filter={props.filter}
                aspects={props.data.aspects}
            />
        );
    }
};

/**
 * Time filter toolbar for aspects view.
 *
 * @param {object} props
 */
const AspectTimeFilter = props => (
    <TimeFilters
        {...props}
        options={[
            { view: Filter.ensure('TODAY') },
            { view: Filter.ensure('PAST_WEEK') },
            { view: Filter.ensure('PAST_MONTH') },
            { view: Filter.ensure('ALL_TIME') }
        ]}
    />
);

/**
 * Main page view for Your Aspects.
 */
export default class AspectsView extends React.Component {
    constructor() {
        super();

        this.state = {
            /* Currently selected filter. */
            filter: Filter.ensure('ALL_TIME'),

            /* Visibility of dialogs. */
            dialogs: {
                'NEW_ASPECT': false
            },

            /* Simpler enforcement of data layer refresh than checking multiple
             * properties for a change. */
            refresh: 0
        };

        this.onChangeFilter = ::this.onChangeFilter;
        this.newAspectDialogAction = ::this.newAspectDialogAction;
    }

    /**
     * Event handler for filter change.
     */
    onChangeFilter(filter) {
        this.setState({ filter: filter });
    }

    /**
     * Event handler for new aspect dialog action.
     */
    newAspectDialogAction(action) {
        if (![DialogButtonActions.CLOSE,
              DialogButtonActions.OPEN,
              DialogButtonActions.SUCCESS].includes(action)) {
                  return;
              }

        this.setState(s => ({
            dialogs: Object.assign(s.dialogs, {
                'NEW_ASPECT': action === DialogButtonActions.OPEN
            })
        }), () => {
            if (action === DialogButtonActions.SUCCESS) {
                /* Force a data layer refresh. */
                this.setState(s => ({ refresh: s.refresh+1 }));
            }
        });
    }

    render() {
        return (
            <div className='view'>
                {this.state.dialogs.NEW_ASPECT && (
                    <NewAspectDialog
                        storeId={this.props.storeId}
                        onAction={this.newAspectDialogAction}
                    />
                )}
                <AspectTimeFilter
                    onChange={this.onChangeFilter}
                    filter={this.state.filter}
                    actionLabel={'+ Ask Something New'}
                    onAction={() => this.newAspectDialogAction(DialogButtonActions.OPEN)}
                />
                <DataLayer action="/api/aspects" data={{
                    store: this.props.storeId,
                    days: Filter.toDays(this.state.filter),
                    points: Filter.toPoints(this.state.filter)
                }} refresh={this.state.refresh}>
                    <AspectContainerLinked
                        filter={this.state.filter}
                    />
                </DataLayer>
            </div>
        );
    }
}
