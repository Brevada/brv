import React from 'react';
import ReactDOM from 'react-dom';
import docReady from 'doc-ready';

import DataLayer from 'forms/DataLayer';
import Loader from 'dashboard/Loader';
import Dashboard from 'dashboard/Dashboard';

const Message = props => (
    <div className='root-loader'>
        <i className='fa fa-exclamation-triangle'></i>
        <div className='tip'>
            <span className='error'>
                {props.text}
                <br /><br />
                Toll Free: <a href='tel:1-855-484-7451' className='phone'>1 (855) 484-7451</a>
            </span>
        </div>
    </div>
);

/**
 * Connects dashboard with data layer and handles multiple store scenarios.
 */
class DashboardLoader extends React.Component {
    constructor(props) {
        super(props);

        /* Default to first store in store collection. */
        this.state = {
            storeId: props.data && props.data.stores && props.data.stores[0].id,
            store: props.data && props.data.stores && props.data.stores[0]
        };

        this.onStoreChange = ::this.onStoreChange;
    }

    componentWillReceiveProps(props) {
        if (props.data && props.data.stores) {
            this.setState({
                storeId: props.data.stores[0].id,
                store: props.data.stores[0]
            });
        }
    }

    shouldComponentUpdate(nextProps, nextState) {
        return this.props.loading !== nextProps.loading ||
               this.state.storeId !== nextState.storeId;
    }

    /**
     * Handles store change event, prompted by user selecting a different
     * store.
     */
    onStoreChange(id) {
        this.setState({
            storeId: id,
            store: this.data.stores.filter(s => s.id === id)[0]
        });
    }

    render() {
        if (this.props.loading) {
            return (
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
            );
        }

        if (!this.props.data.stores) {
            return (
                <Message
                    text={"There are no active stores on your account."}
                />
            );
        }

        if (this.props.data.company_active !== true) {
            if (this.props.data.company_active !== false) {
                return (
                    <Message
                        text={"Your Brevada plan has expired. To renew your plan, please give us a call."}
                    />
                );
            } else {
                return (
                    <Message
                        text={"Your Brevada plan has not been activated. To activate your plan, please give us a call."}
                    />
                );
            }
        }

        if (this.state.storeId) {
            return (
                <Dashboard
                    storeId={this.state.storeId}
                    url={this.state.store.url}
                    stores={this.props.data.stores}
                    onStoreChange={this.onStoreChange}
                />
            );
        }

        return null;
    }
}

docReady(function() {
    ReactDOM.render(
        (<DataLayer action="/api/stores">
            <DashboardLoader />
        </DataLayer>),
        document.getElementById('dashboard-root')
    );
});
