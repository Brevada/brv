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

        this.state = {
            /* Indicates whether at least one aspect has been rated. */
            feedbackGiven: false
        };

        this.onAspectSubmitted = ::this.onAspectSubmitted;
    }

    /**
     * Handler for when feedback has been submitted for an aspect.
     */
    onAspectSubmitted() {
        if (this.state.feedbackGiven) return;

        /* At least one aspect has been rated. */
        this.setState({
            feedbackGiven: true
        });
    }

    render() {
        return (
            <div className='ly flex-v defined-size feedback-container'>
                <Header
                    name={this.props.data.name}
                    onComment={()=>false}
                    onFinish={()=>false}
                    enableFinish={this.state.feedbackGiven}
                />
                <div className='scrollable'>
                    <Aspects
                        aspects={this.props.data.aspects}
                        onSubmit={this.onAspectSubmitted}
                    />
                </div>
            </div>
        );
    }

}
