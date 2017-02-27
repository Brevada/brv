import React from 'react';
import classNames from 'classnames';

/**
 * Header action constants (enum).
 * @type {Object}
 */
const HeaderActions = {
    COMMENT: 'COMMENT',
    SUBMIT_COMMENT: 'SUBMIT_COMMENT',
    SUBMIT_EMAIL: 'SUBMIT_EMAIL',
    FINISH: 'FINISH',
    CLOSE_DIALOG: 'CLOSE_DIALOG'
};

/**
 * Individual header button for use in header controls.
 */
const HeaderButton = props => (
    <div
        className={classNames('btn', 'header-btn', {
            disabled: !!props.disabled,
            negative: !!props.negative
        })}
        onClick={() => !props.disabled && props.onClick()}>
        <span>{props.label}</span>
        <i className={`fa ${props.icon}`}></i>
    </div>
);

/**
 * Header controls for main view. Contains comment and finish buttons.
 */
const FeedbackControls = props => (
    <div className='controls'>
        <HeaderButton
            label='comment'
            icon='fa-commenting-o'
            onClick={()=>props.onAction(HeaderActions.COMMENT)}
        />
        <HeaderButton
            label='finish'
            icon='fa-check-circle-o'
            onClick={()=>props.onAction(HeaderActions.FINISH)}
            disabled={!props.enableFinish}
        />
    </div>
);

/**
 * Header controls for comment dialog.
 */
const CommentControls = props => (
    <div className='controls'>
        <HeaderButton
            label='cancel'
            icon='fa-times-circle'
            negative={true}
            onClick={()=>props.onAction(HeaderActions.CLOSE_DIALOG)}
        />
        <HeaderButton
            label='submit comment'
            icon='fa-check-circle-o'
            onClick={()=>props.onAction(HeaderActions.SUBMIT_COMMENT)}
        />
    </div>
);

/**
 * Header controls for email dialog.
 */
const EmailControls = props => (
 <div className='controls'>
     <HeaderButton
         label='cancel'
         icon='fa-times-circle'
         negative={true}
         onClick={()=>props.onAction(HeaderActions.CLOSE_DIALOG)}
     />
     <HeaderButton
         label='submit email'
         icon='fa-check-circle-o'
         onClick={()=>props.onAction(HeaderActions.SUBMIT_EMAIL)}
     />
 </div>
);

/**
 * Feedback header.
 */
class Header extends React.Component {

    static propTypes = {
        name: React.PropTypes.string,
        onComment: React.PropTypes.func,
        onFinish: React.PropTypes.func
    };

    constructor() {
        super();

        this.state = {     };
        this.getControls = ::this.getControls;
    }

    /**
     * Returns the controls for the current environment.
     */
    getControls() {
        if (!this.props.showDialog) {
            return (
                <FeedbackControls
                    onAction={this.props.onAction}
                    enableFinish={this.props.enableFinish}
                />
            );
        }

        switch (this.props.showDialog) {
            case 'COMMENT':
                return (
                    <CommentControls
                        onAction={this.props.onAction}
                    />
                );
            case 'EMAIL':
                return (
                    <EmailControls
                        onAction={this.props.onAction}
                    />
                );
            default:
                return null;
        }
    }

    render() {
        return (
            <div className='feedback-header'>
                <div className='content'>
                    <div className='brand logo-lq'></div>
                    <div className='heading'>
                        Give <span>{this.props.name}</span> Feedback
                    </div>
                    { this.getControls() }
                </div>
            </div>
        );
    }
}

export { Header as default, HeaderActions };
