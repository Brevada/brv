import React from 'react';

/**
 * Individual header button for use in header controls.
 */
const HeaderButton = props => (
    <div
        className='btn header-btn'
        onClick={props.onClick}>
        <span>{props.label}</span>
        <i className={`fa ${props.icon}`}></i>
    </div>
);

/**
 * Header controls.
 */
const Controls = props => (
    <div className='controls'>
        <HeaderButton
            label='comment'
            icon='fa-commenting-o'
            onClick={props.onComment}
        />
        <HeaderButton
            label='finish'
            icon='fa-check-circle-o'
            onClick={props.onFinish}
        />
    </div>
);

/**
 * Feedback header.
 */
export default class Header extends React.Component {

    static propTypes = {
        name: React.PropTypes.string,
        onComment: React.PropTypes.func,
        onFinish: React.PropTypes.func
    };

    constructor() {
        super();

        this.state = {     };
    }

    render() {
        return (
            <div className='feedback-header'>
                <div className='content'>
                    <div className='brand logo-lq'></div>
                    <div className='heading'>
                        Give <span>{this.props.name}</span> Feedback
                    </div>
                    <Controls
                        onComment={this.props.onComment}
                        onFinish={this.props.onFinish}
                    />
                </div>
            </div>
        );
    }

}
