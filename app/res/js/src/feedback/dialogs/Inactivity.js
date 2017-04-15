import React from 'react';
import Dialog from 'feedback/Dialog';

/**
 * Inactivity dialog.
 */
export default class Inactivity extends React.Component {

    static propTypes = {

    };

    constructor() {
        super();
    }

    render() {
        return (
            <Dialog
                className='dialog-inactivity'
                onOverlayClick={this.props.onClick}
                onContentClick={this.props.onClick}>
                <div className='message'>
                    <span>If you're not done giving feedback, tap here.</span>
                    <i className='fa fa-clock-o'></i>
                </div>
            </Dialog>
        );
    }

}
