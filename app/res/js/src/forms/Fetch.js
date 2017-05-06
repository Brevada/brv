import React, { Component } from 'react';
import PropTypes from 'prop-types';
import _ from 'lodash';
import axios from 'axios';
import setStatePromise from 'utils/StatePromise';

/**
 * API getter layer which retrieves data from the backend and passes it to
 * its children.
 */
export default class Fetch extends Component {

    /* No default properties. */
    static defaultProps = {};

    static propTypes = {
        /* Numeric property to force refresh the data. */
        refresh: PropTypes.number,

        /* The API endpoint. */
        action: PropTypes.string.isRequired,

        /* Optional parameters to send to server. */
        data: PropTypes.object,

        /* Optional write cache function, to store copy of latest fetched data. */
        writeCache: PropTypes.func,

        /* Optional read cache function to call if unable to fetch. */
        readCache: PropTypes.func,

        /* Success and error callbacks for AJAX response. */
        onSuccess: PropTypes.func,
        onError: PropTypes.func
    };

    constructor() {
        super();

        this.state = {
            /* Indicates loading state. */
            loading: false,

            /* Result of the GET request. */
            result: {},

            /* Error if API request was unsuccessful. */
            error: null
        };

        this.load = ::this.load;
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    componentDidMount() {
        /* Skip if refresh is -1, i.e. "inactive" mode. */
        if (this.props.refresh !== -1) this.load();
    }

    componentWillReceiveProps(nextProps) {
        if (this._unmounted) return;

        /* Allow "forced" refresh using props.refresh. Otherwise, if not forced
         * only repeat request when request has changed. */

        if ((this.props.refresh !== nextProps.refresh ||
            this.props.action !== nextProps.action ||
            !_.isEqual(nextProps.data || {}, this.props.data || {})) &&
            nextProps.refresh !== -1) {
            this.load();
        }
    }

    /**
     * Perform API request.
     */
    load() {
        if (this._unmounted) return;

        /* Skip if already downloading. */
        if (this.state.loading) return;

        const data = { params: Object.assign({}, this.props.data) };
        const ajax = (window.brv && window.brv.feedback) ?
                     window.brv.feedback.interceptor || axios :
                     axios;

        setStatePromise.call(this, {
            /* Now loading. Clear errors. */
            loading: true,
            error: null
        }).then(() => ajaz(Object.assign(
            { url: this.props.action, method: 'get' },
            data
        ))).then(response => {
            if (this._unmounted) return;

            return setStatePromise.call(this, {
                /* Store response. */
                result: response.data,
                error: null
            });
        }).then(() => {
            /* Write to cache if available. */
            if (this.props.writeCache && typeof this.props.writeCache === 'function') {
                /* Makes use of cache, save data to cache. */
                this.props.writeCache(response.data || {}).catch(() => false);
            }

            /* Notify onSuccess. */
            this.props.onSuccess && this.props.onSuccess(response)
        }).catch(error => {
            if (this._unmounted) return;

            if (this.props.readCache && typeof this.props.readCache === 'function') {
                /* Upon failure check for cache. */
                return Promise.resolve(this.props.readCache()).then(cached => (
                    setStatePromise.call(this, {
                        result: cachedData,
                        error: null
                    })
                )).then(() => (
                    this.props.onSuccess && this.props.onSuccess({
                        data: cachedData
                    })
                )).catch(() => (
                    setStatePromise.call(this, {
                        result: {},
                        error: error.response || error
                    }).then(state => {
                        this.props.onError && this.props.onError(state.error)
                    })
                ));
            } else {
                return setStatePromise.call(this, {
                    result: {},
                    error: error.response || error
                }).then(() => {
                    this.props.onError && this.props.onError(this.state.error)
                });
            }
        }).then(() => {
            if (this._unmounted) return;
            this.setState({ loading: false });
        });
    }

    render() {
        /* Clone element, passing in retrieved data. */
        return React.cloneElement(
            React.Children.only(this.props.children), {
                data: this.state.result,
                loading: this.state.loading,
                error: this.state.error
            }
        );
    }
}
