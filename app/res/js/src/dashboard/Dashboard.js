import React from 'react';
import ReactDOM from 'react-dom';
import LazilyLoad, { importLazy } from 'utils/LazilyLoad';
import classNames from 'classnames';

import DataLayer from 'forms/DataLayer';

import NavigationBar from 'dashboard/NavigationBar';
import LiveFeed from 'dashboard/livefeed/LiveFeed';

/* Views */

export default class Dashboard extends React.Component {
    constructor() {
        super();

        this.state = {
            view: 'EVENTS'
        };

        this.onChangeView = this.onChangeView.bind(this);
        this.getView = this.getView.bind(this);
    }

    onChangeView(view) {
        this.setState({ view: view });
    }

    getView(view) {
        /* Hardcoded (for the moment) since webpack prints a warning if an
         * expression is used in an import. */
        const views = {
            'ASPECTS': (
                <LazilyLoad modules={{
                    C: () => importLazy(import('dashboard/aspects/AspectsView'))
                }}>{({C})=>(<C storeId={this.props.storeId} />)}</LazilyLoad>
            ),
            'EVENTS': (
                <LazilyLoad modules={{
                    C: () => importLazy(import('dashboard/events/EventsView'))
                }}>{({C})=>(<C storeId={this.props.storeId} />)}</LazilyLoad>
            ),
            'TIMELINE': (
                <LazilyLoad modules={{
                    C: () => importLazy(import('dashboard/TimelineView'))
                }}>{({C})=>(<C storeId={this.props.storeId} />)}</LazilyLoad>
            )
        };

        return views[view];
    }

    render() {
        return (
            <div className='dashboard-container'>
                <div className='left-column'>
                    <NavigationBar
                        onChangeView={this.onChangeView}
                        view={this.state.view}
                        url={this.props.data.url}
                    />
                    { this.getView(this.state.view) }
                </div>
                <div className='right-column'>
                    <LiveFeed />
                </div>
            </div>
        );
    }

}
