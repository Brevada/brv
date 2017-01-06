import React from 'react';
import ReactDOM from 'react-dom';

import Aspect from './Aspect';

export default class AspectContainer extends React.Component {

    constructor() {
        super();
        this.state = {};
    }

    render() {
        return (
            <div className='aspect-container'>
                {this.props.aspects
                    .concat()
                    .sort((a,b) => a.title.localeCompare(b.title))
                    .map(aspect => (
                        <Aspect
                            key={aspect.id}
                            id={aspect.id}
                            title={aspect.title}
                            summary={aspect.summary}
                            filter={this.props.filter}
                            onRemove={this.props.onRemove}
                        />
                ))}
            </div>
        );
    }

}
