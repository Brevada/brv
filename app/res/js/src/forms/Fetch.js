import React, { Component } from "react";
import PropTypes from "prop-types";
import _ from "lodash";
import ajax from "utils/Ajax";
import setStatePromise from "utils/StatePromise";

/**
 * API getter layer which retrieves data from the backend and passes it to
 * its children.
 */
export default class Fetch extends Component {

    /* No default properties. */
    static defaultProps = {
        refresh: 0,
        data: {},
        writeCache: null,
        readCache: null,
        onSuccess: () => { /* no op */ },
        onError: () => { /* no op */ }
    };

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
        onError: PropTypes.func,

        children: PropTypes.element.isRequired
    };

    /**
     * @constructor
     */
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

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * @override
     */
    componentDidMount() {
        /* Skip if refresh is -1, i.e. "inactive" mode. */
        if (this.props.refresh !== -1) this.load();
    }

    /**
     * @override
     */
    componentWillReceiveProps(nextProps) {
        if (this._unmounted || nextProps.refresh === -1) return;

        /* Allow "forced" refresh using props.refresh. Otherwise, if not forced
         * only repeat request when request has changed. */

         /**
          * Gets version of props to be used with a deep comparison to
          * determine if a load should occur.
          * @param   {object} src The source props.
          * @returns {object}
          */
        const getComparable = src => (
            _.defaults(
                _.pick(src, ["refresh", "action", "data"]),
                { data: {} }
            )
        );

        const pOldProps = getComparable(this.props),
            pNewProps = getComparable(nextProps);

        if (!_.isEqual(pOldProps, pNewProps)) {
            this.load();
        }
    }

    /**
     * Performs API fetch.
     * @returns {Promise} Resolves to ajax/axios response.
     */
    fetch() {
        const data = { params: Object.assign({}, this.props.data) };

        const prom = ajax(Object.assign(
            { url: this.props.action, method: "get" },
            data
        ));

        return prom;
    }

    /**
     * Handles a successful fetch, caching if a caching function is available.
     * @param   {object} response Ajax response.
     * @returns {void}
     */
    onSuccessfulFetch(response) {
        /* Write to cache if available. */
        if (this.props.writeCache && typeof this.props.writeCache === "function") {

            /* Makes use of cache, save data to cache. */
            this.props.writeCache(response.data || {}).catch(() => false);
        }

        /* Notify onSuccess. */
        this.props.onSuccess && this.props.onSuccess(response);
    }

    /**
     * Handles a failed fetch, reading from cache as a backup if available.
     * @param   {object} error Ajax error response
     * @returns {Promise}      Resolves or Rejects (undefined)
     */
    onFailedFetch(error) {
        if (this._unmounted) return Promise.reject();

        if (this.props.readCache && typeof this.props.readCache === "function") {

            /* Upon failure check for cache. */
            return Promise.resolve(this.props.readCache()).then(cached => (
                setStatePromise.call(this, {
                    result: cached,
                    error: null
                }).then(() => cached)
            ))
            .then(cached => (
                this.props.onSuccess && this.props.onSuccess({
                    data: cached
                })
            ))
            .catch(() =>
                setStatePromise.call(this, {
                    result: {},
                    error: error.response || error
                }).then(state => {
                    this.props.onError && this.props.onError(state.error);
                })
            );
        }

        return setStatePromise.call(this, {
            result: {},
            error: error.response || error
        }).then(() => {
            this.props.onError && this.props.onError(this.state.error);
        });
    }

    /**
     * Perform API request. Differs from doFetch in that it handles the response.
     * @returns {void}
     */
    load() {
        /* Skip if unmounted or already downloading. */
        if (this._unmounted || this.state.loading) return;

        setStatePromise.call(this, {
            /* Now loading. Clear errors. */
            loading: true,
            error: null
        })
        .then(() => this.fetch())
        .then(response => ( /* Store response. */
            setStatePromise.call(this, {
                result: response.data,
                error: null
            })
        ))
        .then(response => this.onSuccessfulFetch(response))
        .catch(error => this.onFailedFetch(error))
        .then(() => (
            setStatePromise.call(this, {
                loading: false
            })
        ));
    }

    /**
     * @override
     */
    render() {

        /* Clone element, passing in retrieved data. */
        return React.cloneElement(
            React.Children.only(this.props.children), Object.assign({
                data: this.state.result,
                loading: this.state.loading,
                error: this.state.error
            })
        );
    }
}
