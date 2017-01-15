import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import equal from 'deep-equal';

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
        this.load();
    }

    componentWillReceiveProps(nextProps) {
        if (!this._unmounted && !equal(nextProps, this.props)) {
            this.load();
        }
    }

    load() {
        // Skip if already downloading.
        if (this.state.loading) return;

        this.setState({
            loading: true,
            error: null
        }, () => {
            /* For debugging. */
            if (this.props.dummy) {
                setTimeout(() => {
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
                if (!this._unmounted) {
                    this.setState({ loading: false });
                }
            });
        });
    }

    render() {
        return React.cloneElement(React.Children.only(this.props.children), {
            data: this.state.result,
            loading: this.state.loading,
            error: this.state.error
        });
    }
}
