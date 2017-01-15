import React from 'react';
import ReactDOM from 'react-dom';

import { Link } from 'components/Link';
import { Filter } from 'dashboard/aspects/Filter';

import AspectContainer from 'dashboard/aspects/AspectContainer';
import TimeFilters from 'dashboard/TimeFilters';

import { DialogButtonActions } from 'dialogs/Dialog';
import NewAspectDialog from 'dialogs/NewAspectDialog';
import Loader from 'dashboard/Loader';

import DataLayer from 'forms/DataLayer';

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

export default class AspectsView extends React.Component {
    constructor() {
        super();

        this.state = {
            filter: 'TODAY',
            dialogs: {
                'NEW_ASPECT': false
            },
            refresh: 0
        };

        this.onChangeFilter = this.onChangeFilter.bind(this);
        this.newAspectDialogAction = this.newAspectDialogAction.bind(this);
    }

    onChangeFilter(filter) {
        this.setState({ filter: filter });
    }

    newAspectDialogAction(action) {
        if (![DialogButtonActions.CLOSE,
              DialogButtonActions.OPEN,
              DialogButtonActions.SUCCESS].includes(action)) {
                  return;
              }

        this.setState({
            dialogs: Object.assign(this.state.dialogs, {
                'NEW_ASPECT': action === DialogButtonActions.OPEN
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
                {this.state.dialogs.NEW_ASPECT && (
                    <NewAspectDialog
                        storeId={this.props.storeId}
                        onAction={this.newAspectDialogAction}
                    />
                )}
                <TimeFilters
                    onChangeFilter={this.onChangeFilter}
                    filter={this.state.filter}
                    actionLabel='+ Ask Something New'
                    onAction={() => {this.newAspectDialogAction(DialogButtonActions.OPEN);}}
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
