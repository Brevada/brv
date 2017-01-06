import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

import { Link } from '../components/Link';
import { Filter } from './aspects/Filter';

import AspectContainer from './aspects/AspectContainer';
import TimeFilters from './TimeFilters';

import { DialogButtonActions } from '../dialogs/Dialog';
import NewAspectDialog from '../dialogs/NewAspectDialog';

import Loader from './Loader';

export default class AspectsView extends React.Component {
    constructor() {
        super();

        this.state = {
            filter: 'TODAY',
            dialogs: {
                'NEW_ASPECT': false
            },

            loading: false,
            error: null,
            aspects: []
        };

        this.onChangeFilter = this.onChangeFilter.bind(this);
        this.newAspectDialogAction = this.newAspectDialogAction.bind(this);
        this.fetchAspects = this.fetchAspects.bind(this);
        this.onRemove = this.onRemove.bind(this);
    }

    componentWillReceiveProps(next) {
        if (!this.props.storeId && !!next.storeId) {
            this.fetchAspects();
        }
    }

    componentDidMount() {
        this.fetchAspects();
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    fetchAspects() {
        if (!this.props.storeId || this.state.loading) return;

        this.setState({
            loading: true
        }, () => {
            axios.get('/api/aspects', {
                params: {
                    store: this.props.storeId,
                    days: Filter.toDays(this.state.filter),
                    points: Filter.toPoints(this.state.filter)
                }
            })
            .then(res => {
                if (this._unmounted) return;
                this.setState({
                    loading: false,
                    aspects: res.data.aspects || []
                });
            })
            .catch(err => {
                if (this._unmounted) return;
                this.setState({
                    loading: false,
                    error: err.reason || `An unknown error has occured: ${err.code}`
                });
            });
        });
    }

    onRemove(id) {
        this.setState({ aspects: this.state.aspects.filter(a => a.id !== id) });
    }

    onChangeFilter(filter) {
        this.setState({ filter: filter }, this.fetchAspects);
    }

    newAspectDialogAction(action) {
        if (action === DialogButtonActions.CLOSE) {
            this.setState({ dialogs: Object.assign(this.state.dialogs, {
                'NEW_ASPECT': false
            }) });
        } else if (action === DialogButtonActions.OPEN) {
            this.setState({ dialogs: Object.assign(this.state.dialogs, {
                'NEW_ASPECT': true
            }) });
        } else if (action === DialogButtonActions.SUCCESS) {
            this.setState({ dialogs: Object.assign(this.state.dialogs, {
                'NEW_ASPECT': false
            }) }, () => {
                this.fetchAspects();
            });
        }
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
                    onNewAspect={() => {this.newAspectDialogAction(DialogButtonActions.OPEN);}} />
                { !this.state.loading && (
                    <AspectContainer
                        filter={this.state.filter}
                        aspects={this.state.aspects}
                        loading={this.state.loading}
                        onRemove={this.onRemove}
                    />
                ) }
                { this.state.loading && (
                    <Loader
                        messages={['Loading aspects...']}
                    />
                ) }
            </div>
        );
    }

}
