import React from 'react';

export default class Dialog extends React.Component {
    constructor() {
        super();
        this.onAttention = ::this.onAttention;
    }

    componentDidMount() {
        brv && brv.feedback && brv.feedback.scroll.lock();
    }

    componentWillUnmount() {
        brv && brv.feedback && brv.feedback.scroll.lock(false);
    }

    /**
     * Handles dialog onClick/onFocus event. We'll use this event to redirect
     * input focus.
     */
    onAttention() {
        if (this.props.onAttention) this.props.onAttention();
    }

    render() {
        return (
            <div
                className={'dialog-overlay ' + (this.props.className || '')}
                onClick={this.onAttention}
                onFocus={this.onAttention}>
                <div className='dialog-content'>
                    {this.props.children}
                </div>
            </div>
        );
    }
}
