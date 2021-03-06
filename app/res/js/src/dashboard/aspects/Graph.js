import React, { Component } from "react";
import PropTypes from "prop-types";
import _ from "lodash";

import { Filter } from "dashboard/aspects/Filter";
import { DateCluster } from "utils/DateCluster";
import { LineChart } from "dashboard/aspects/graph/LineChart";

/**
 * Aspect line chart graph.
 */
class Graph extends Component {

    static propTypes = {
        data: PropTypes.array,
        filter: PropTypes.string.isRequired
    };

    static defaultProps = {
        data: []
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {
            data: []
        };

        /* Used as internal DS. Don't want to affect state. */
        this.timestamps = [];
        this.dates = [];

        this.updateData = ::this.updateData;
    }

    /**
     * @override
     */
    shouldComponentUpdate(nextProps, nextState) {
        return !_.isEqual(nextState.data, this.state.data);
    }

    /**
     * @override
     */
    componentWillReceiveProps(nextProps) {
        /* Only update the data if there is NEW data. */
        if (!_.isEqual(nextProps.data, this.props.data)) this.updateData();
    }

    /**
     * @override
     */
    componentDidMount() {
        this.updateData();
    }

    /**
     * Calculates date groupings.
     * @returns {void}
     */
    updateData() {
        this.timestamps = this.props.data.map(d => Math.floor((d.from + d.to) / 2));

        let formatter = {};

        if (this.props.filter === Filter.ensure("TODAY")) {
            formatter = {
                /* Override default date formatter. */
                day: d => d.format("ddd").substring(0, 1)
            };
        }

        this.dates = DateCluster.getLabels(this.timestamps, formatter);

        let lastValue = null;

        this.setState({
            data: this.props.data.map((datum, index) => {
                lastValue = datum.average || lastValue || 0;

                return {
                    value: +lastValue.toFixed(2),
                    label: index,
                    empty: datum.average === null
                };
            })
        });
    }

    /**
     * @override
     */
    render() {
        /* Computed values are passed into a purer component. */
        return (
            <div className="graph">
                <LineChart
                    data={this.state.data}
                    dates={this.dates}
                    timestamps={this.timestamps}
                />
            </div>
        );
    }
}

export { Graph };
