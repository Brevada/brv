import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import _ from 'lodash';
import axios from 'axios';

export default class DataLayer extends React.Component {
    constructor() {
        super();

        this.state = {
            loading: false,
            result: {},
            error: null
        };

        this.load = this.load.bind(this);
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    componentDidMount() {
        // Skip if refresh is -1, i.e. "inactive" mode.
        if (this.props.refresh === -1) return;

        this.load();
    }

    componentWillReceiveProps(nextProps) {
        if (this._unmounted) return;

        if ((this.props.refresh !== nextProps.refresh ||
            this.props.action !== nextProps.action ||
            !_.isEqual(nextProps.data || {}, this.props.data || {})) &&
            nextProps.refresh !== -1) {
            this.load();
        }
    }

    load() {
        if (this._unmounted) return;

        // Skip if already downloading.
        if (this.state.loading) return;

        this.setState({
            loading: true,
            error: null
        }, () => {
            /* For debugging. */
            if (this.props.dummy) {
                setTimeout(() => {
                    if (this._unmounted) return;

                    this.setState({
                        loading: false,
                        error: null,
                        result: this.props.dummy || {}
                    }, () => {
                        if (this.props.onSuccess && this.props.dummy !== false) {
                            this.props.onSuccess(this.props.dummy);
                        } else if (this.props.onError && this.props.dummy === false) {
                            this.props.onError(this.props.dummy);
                        }
                    });
                }, this.props.dummyDelay || 0);
                return;
            }
            /* End of debugging code. */

            let data = { params: Object.assign({}, this.props.data) };

            axios(Object.assign({
                url: this.props.action
            }, data, { method: 'get' }))
            .then(response => {
                if (this._unmounted) return;
                this.setState({
                    result: response.data,
                    error: null
                }, () => {
                    if (this.props.onSuccess) {
                        this.props.onSuccess(response);
                    }
                });
            })
            .catch(error => {
                if (this._unmounted) return;
                this.setState({
                    result: {},
                    error: error.response || error
                }, () => {
                    if (this.props.onError) {
                        this.props.onError(this.state.error);
                    }
                });
            })
            .then(() => {
                if (this._unmounted) return;
                this.setState({ loading: false });
            });
        });
    }

    render() {
        return React.cloneElement(React.Children.only(this.props.children), Object.assign({
            data: this.state.result,
            loading: this.state.loading,
            error: this.state.error
        }, _.omit(this.props, [
            'children', 'data', 'loading', 'error', 'onSuccess',
            'onError', 'action', 'dummy', 'dummyDelay', 'refresh', 'key'
        ])));
    }
}
