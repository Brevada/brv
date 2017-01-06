import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import classNames from 'classnames';

import NavigationBar from './NavigationBar';
import LiveFeed from './LiveFeed';

/* Views */
import TimelineView from './TimelineView';
import AspectsView from './AspectsView';
import EventsView from './EventsView';
import Loader from './Loader';

export default class Dashboard extends React.Component {

    constructor() {
        super();

        this.state = {
            view: 'ASPECTS',

            loading: true,
            error: null,

            storeId: null,
            storeName: null,
            storeActive: null,
            storeUrl: null
        };

        this.onChangeView = this.onChangeView.bind(this);
    }

    componentDidMount() {
        axios.get('/api/store')
        .then(res => {
            this.setState({
                loading: false,
                storeId: res.data.id,
                storeName: res.data.name,
                storeActive: res.data.active,
                storeUrl: res.data.url
            });
        })
        .catch(err => {
            this.setState({
                loading: false,
                error: err.reason || `An unknown error has occured: ${err.code}`
            });
        });
    }

    onChangeView(view) {
        this.setState({ view: view });
    }

    render() {
        const views = {
            'TIMELINE': <TimelineView storeId={this.state.storeId} />,
            'ASPECTS': <AspectsView storeId={this.state.storeId} />,
            'EVENTS': <EventsView storeId={this.state.storeId} />
        };

        return (
            <div className={classNames('dashboard-container', { loading: this.state.loading })}>
                <div className='left-column'>
                    <NavigationBar
                        onChangeView={this.onChangeView}
                        view={this.state.view}
                        url={this.state.storeUrl}
                    />
                    {!this.state.loading && views[this.state.view]}
                    {this.state.loading && (
                        <Loader
                            className='view'
                            messages={[
                                "Preparing your dashboard...",
                                "Retrieving store information...",
                                "Downloading analytics...",
                                "Analyzing data...",
                                "Crunching numbers..."
                            ]}
                        />
                    )}
                </div>
                <div className='right-column'>
                    <LiveFeed />
                </div>
            </div>
        );
    }

}
