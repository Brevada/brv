import React from 'react';
import ReactDOM from 'react-dom';
import equal from 'deep-equal';

import Aspect from 'dashboard/aspects/Aspect';
import Loader from 'dashboard/Loader';

export default class AspectContainer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            aspects: props.aspects || [],
            removed: []
        };

        this.onRemove = this.onRemove.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        if (!nextProps.aspects || equal(nextProps.aspects, this.state.aspects)) {
            return;
        }

        this.setState({
            aspects: nextProps.aspects
        });
    }

    onRemove(id) {
        this.setState({
            removed: this.state.removed.concat([id])
        });
    }

    render() {
        return (
            <div className='aspect-container'>
                {this.state.aspects
                    .concat()
                    .filter(a => !this.state.removed.includes(a.id))
                    .sort((a,b) => a.title.localeCompare(b.title))
                    .map(aspect => (
                        <Aspect
                            key={aspect.id}
                            id={aspect.id}
                            title={aspect.title}
                            summary={aspect.summary}
                            filter={this.props.filter}
                            onRemove={this.onRemove}
                        />
                ))}
            </div>
        );
    }

}
