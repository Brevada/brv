import React from 'react';

import Header from 'feedback/Header';
import Aspects from 'feedback/Aspects';

/**
 * The feedback view.
 */
export default class Feedback extends React.Component {

    static propTypes = {
        storeId: React.PropTypes.number.isRequired
    };

    constructor() {
        super();

        this.state = {     };
    }

    render() {
        return (
            <div className='ly flex-v ly-split feedback-container'>
                <Header
                    name={this.props.data.name}
                    onComment={()=>false}
                    onFinish={()=>false}
                />
                <div className='scrollable fill'>
                    <Aspects
                        aspects={this.props.data.aspects}
                    />
                </div>
            </div>
        );
    }

}
