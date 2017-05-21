import React, { Component } from "react";
import ReactDOM from "react-dom";
import PropTypes from "prop-types";
import docReady from "doc-ready";

import Fetch from "forms/Fetch";
import Loader from "dashboard/Loader";
import Dashboard from "dashboard/Dashboard";

/**
 * Dashboard important status message (regarding state of dashboard).
 * @param   {object} props React props.
 * @param   {string} text The message to display.
 * @returns {JSX}
 */
const Message = props => (
    <div className="root-loader">
        <i className="fa fa-exclamation-triangle"></i>
        <div className="tip">
            <span className="error">
                {props.text}
                <br /><br />
                {"Toll Free: "}
                <a href="tel:1-855-484-7451" className="phone">
                    {"1 (855) 484-7451"}
                </a>
            </span>
        </div>
    </div>
);

Message.propTypes = {
    text: PropTypes.string.isRequired
};

/**
 * Connects dashboard with data layer and handles multiple store scenarios.
 */
class DashboardLoader extends Component {

    static propTypes = {
        loading: PropTypes.bool,
        error: PropTypes.object,
        data: PropTypes.object
    };

    static defaultProps = {
        error: null,
        data: {},
        loading: true
    };

    /**
     * @constructor
     * @param {object} props React props.
     */
    constructor(props) {
        super(props);

        this.state = {};

        this.onStoreChange = ::this.onStoreChange;
    }

    /**
     * componentWillReceiveProps
     * @param   {object} props React props.
     * @returns {void}
     */
    componentWillReceiveProps(props) {
        if (props.data && props.data.stores) {
            const [defaultStore] = (props.data && props.data.stores) || [];

            this.setState({
                storeId: defaultStore.id,
                store: defaultStore
            });
        }
    }

    /**
     * shouldComponentUpdate
     * @param   {[type]} nextProps New props.
     * @param   {[type]} nextState New state.
     * @returns {boolean}
     */
    shouldComponentUpdate(nextProps, nextState) {
        return this.props.loading !== nextProps.loading ||
               this.state.storeId !== nextState.storeId;
    }

    /**
     * Handles store change event, prompted by user selecting a different
     * store.
     * @param   {number} storeId The store id to change to.
     * @returns {void}
     */
    onStoreChange(storeId) {
        const [store] = this.props.data.stores.filter(s => s.id === storeId);

        this.setState({
            storeId,
            store
        });
    }

    /**
     * Returns a loader animation component.
     * @returns {JSX}
     */
    static getLoader() {
        return (
            <Loader
                className="view"
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

    /* eslint-disable complexity */
    /**
     * @override
     */
    render() {
        if (this.props.loading) return DashboardLoader.getLoader();

        /* eslint-disable no-nested-ternary */
        const msg = this.props.error ?
                    "An unexpected error has occured. You may try refresh the page." :
                    !this.props.data.stores ?
                    "There are no active stores on your account." :
                    this.props.data.company_active !== true && this.props.data.company_active !== false ?
                    "Your Brevada plan has expired. To renew your plan, please give us a call." :
                    this.props.data.company_active !== true && this.props.data.company_active === false ?
                    "Your Brevada plan has not been activated. To activate your plan, please give us a call." :
                    null;
        /* eslint-enable no-nested-ternary */

        if (msg) return (<Message text={msg} />);

        return (
            <Dashboard
                storeId={this.state.storeId}
                url={this.state.store.url}
                stores={this.props.data.stores}
                onStoreChange={this.onStoreChange}
            />
        );
    }
    /* eslint-enable complexity */
}

docReady(() => {
    ReactDOM.render(
        (<Fetch action="/api/stores">
            <DashboardLoader />
        </Fetch>),
        document.getElementById("dashboard-root")
    );
});
