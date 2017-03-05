import React from 'react';

export default class Dialog extends React.Component {
    constructor() { super(); }

    componentDidMount() {
        brv && brv.feedback && brv.feedback.scroll.lock();
    }

    componentWillUnmount() {
        brv && brv.feedback && brv.feedback.scroll.lock(false);
    }

    render() {
        return (
            <div className={'dialog-overlay ' + (this.props.className || '')}>
                <div className='dialog-content'>
                    {this.props.children}
                </div>
            </div>
        );
    }
}
