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
    <div className={classNames('controls', {
        'single': !props.enableComments
    })}>
        { props.enableComments && (
            <HeaderButton
                label='comment'
                icon='fa-commenting-o'
                onClick={()=>props.onAction(HeaderActions.COMMENT)}
            />
        ) }
        <HeaderButton
            label='finish'
            icon='fa-check-circle-o'
            onClick={()=>props.onAction(HeaderActions.FINISH)}
            disabled={!props.enableSubmit}
        />
    </div>
);

/**
 * Header controls for comment dialog.
 */
const CommentControls = props => {
    let lastForm = brv.feedback.session.getRemainingCount() === 0;

    return (
        <div className='controls'>
            <HeaderButton
                label={lastForm ? 'skip' : 'cancel'}
                icon='fa-times-circle'
                negative={true}
                onClick={()=>props.onAction(HeaderActions.CLOSE_DIALOG)}
            />
            <HeaderButton
                label='submit comment'
                icon='fa-check-circle-o'
                onClick={()=>props.onAction(HeaderActions.SUBMIT_COMMENT)}
                disabled={!props.enableSubmit}
            />
        </div>
    );
};

/**
 * Header controls for email dialog.
 */
const EmailControls = props => (
 <div className='controls'>
     <HeaderButton
         label='skip'
         icon='fa-times-circle'
         negative={true}
         onClick={()=>props.onAction(HeaderActions.CLOSE_DIALOG)}
     />
     <HeaderButton
         label='submit email'
         icon='fa-check-circle-o'
         onClick={()=>props.onAction(HeaderActions.SUBMIT_EMAIL)}
         disabled={!props.enableSubmit}
     />
 </div>
);

/**
 * Minified & simplistic header.
 */
const MinyHeader = props => (
    <div className='feedback-header miny-header'>
        <div className='content'>
            <div className='brand logo-lq'></div>
            <div className='heading'>
                Give <span>{props.name}</span> Feedback
            </div>
        </div>
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
        const props = {
            onAction: this.props.onAction,
            enableSubmit: this.props.enableSubmit
        };

        if (!this.props.showDialog) {
            return (
                <FeedbackControls
                    {...props}
                    enableComments={this.props.enableComments}
                />
            );
        }

        switch (this.props.showDialog) {
            case 'COMMENT':
                return (<CommentControls {...props} />);
            case 'EMAIL':
                return (<EmailControls {...props} />);
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

export { Header as default, MinyHeader, HeaderActions };
