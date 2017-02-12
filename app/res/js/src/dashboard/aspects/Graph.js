import React from 'react';
import _ from 'lodash';

import { DateCluster } from 'utils/DateCluster';
import { LineChart } from 'dashboard/aspects/graph/LineChart';

/**
 * Aspect line chart graph.
 */
class Graph extends React.Component {

    static propTypes = {
        data: React.PropTypes.array
    };

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

    shouldComponentUpdate(nextProps, nextState) {
        return !_.isEqual(nextState.data, this.state.data);
    }

    componentWillReceiveProps(nextProps) {
        /* Only update the data if there is NEW data. */
        if (!_.isEqual(nextProps.data, this.props.data)) this.updateData();
    }

    componentDidMount() {
        this.updateData();
    }

    /**
     * Calculates date groupings.
     */
    updateData() {
        this.timestamps = this.props.data.map(d => Math.floor((d.from+d.to)/2));
        this.dates = DateCluster.getLabels(this.timestamps, {
            /* Override default date formatter. */
            day: d => d.format('ddd').substring(0,1)
        });

        this.setState({
            data: this.props.data.map((datum, index) => (
                {
                    value: datum.average === null ? null : +datum.average.toFixed(2),
                    label: index
                }
            ))
        });
    }

    render() {
        /* Computed values are passed into a purer component. */
        return (
            <div className='graph'>
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
