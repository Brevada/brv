import React from 'react';
import LazilyLoad, { importLazy } from 'utils/LazilyLoad';
import NavigationBar from 'dashboard/NavigationBar';
import LiveFeed from 'dashboard/livefeed/LiveFeed';

/**
 * The dashboard view. Contains "subviews".
 */
export default class Dashboard extends React.Component {

    static propTypes = {
        /* Each dashboard is an instance of a specific store. */
        storeId: React.PropTypes.number.isRequired
    };

    constructor() {
        super();

        /* Pull default view from URL hash. */
        let defaultView = (window.location.hash || '#').substring(1).trim().toUpperCase();
        if (!['ASPECTS', 'EVENTS', 'TIMELINE'].includes(defaultView)) {
            defaultView = 'ASPECTS';
        }

        this.state = {
            /* The currently loaded view. */
            view: defaultView
        };

        this.onChangeView = ::this.onChangeView;
        this.getView = ::this.getView;
    }

    /**
     * Triggers an internal view change.
     * @param {string} view The lookup key for the view to change to.
     */
    onChangeView(view) {
        this.setState({ view: view });
    }

    /**
     * Returns the JSX view (page) to load into the dashboard. Each view is
     * lazily loaded - i.e. loaded on demand.
     *
     * @param {string} view The lookup key for the view to return.
     */
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
