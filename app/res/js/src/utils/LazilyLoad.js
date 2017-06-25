/* eslint-disable */

/*
 * Straight from https://webpack.js.org/guides/lazy-load-react/
 */

import React, { Component } from "react";
import PropTypes from "prop-types";

/**
 * Enables lazy loading of components.
 */
class LazilyLoad extends Component {

    /**
     * @constructor
     */
    constructor() {
        super(...arguments);
        this.state = {
            isLoaded: false
        };
    }

    /**
     * componentDidMount
     * @returns {void}
     */
    componentDidMount() {
        this._isMounted = true;
        this.load();
    }

    /**
     * componentDidUpdate
     * @param   {object} previous Previous props.
     * @returns {void}
     */
    componentDidUpdate(previous) {
        if (this.props.modules !== previous.modules) this.load();
    }

    /**
     * componentWillUnmount
     * @returns {void}
     */
    componentWillUnmount() {
        this._isMounted = false;
    }

    /**
     * Loads lazily loaded module.
     * @returns {void}
     */
    load() {
        this.setState({
            isLoaded: false
        });

        const { modules } = this.props;
        const keys = Object.keys(modules);

        Promise
        .all(keys.map((key) => modules[key]()))
        .then((values) => keys.reduce((agg, key, index) => {
            agg[key] = values[index];

            return agg;
        }, {}))
        .then((result) => {
          if (this._isMounted) {
              this.setState({ modules: result, isLoaded: true });
          }
        });
    }

    /**
     * @override
     */
    render() {
        if (!this.state.isLoaded) return null;
        return React.Children.only(this.props.children(this.state.modules));
    }
}

LazilyLoad.propTypes = {
    children: PropTypes.func.isRequired
};

/**
 * [LazilyLoadFactory description]
 * @param   {Component} Component React Component
 * @param   {object}    modules Component loader callback.
 * @returns {function}
 */
export const LazilyLoadFactory = (Component, modules) => {
    return props => (
        <LazilyLoad modules={modules}>
            {(mods) => <Component {...mods} {...props} />}
        </LazilyLoad>
    );
};

/**
 * Import Lazy helper
 * @param   {Promise} promise Promise
 * @returns {Promise}
 */
export const importLazy = promise => (
    promise.then((result) => result.default)
);

export default LazilyLoad;
