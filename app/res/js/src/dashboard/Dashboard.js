import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';

import DataLayer from 'forms/DataLayer';

import NavigationBar from 'dashboard/NavigationBar';
import LiveFeed from 'dashboard/livefeed/LiveFeed';

/* Views */
import TimelineView from 'dashboard/TimelineView';
import AspectsView from 'dashboard/aspects/AspectsView';
import EventsView from 'dashboard/events/EventsView';


export default class Dashboard extends React.Component {
    constructor() {
        super();

        this.state = {
            view: 'ASPECTS'
        };

        this.onChangeView = this.onChangeView.bind(this);
    }

    onChangeView(view) {
        this.setState({ view: view });
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
                    {{
                        'TIMELINE': <TimelineView storeId={this.props.data.id} />,
                        'ASPECTS': <AspectsView storeId={this.props.data.id} />,
                        'EVENTS': <EventsView storeId={this.props.data.id} />
                    }[this.state.view] }
                </div>
                <div className='right-column'>
                    <LiveFeed />
                </div>
            </div>
        );
    }

}
