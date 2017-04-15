import React from 'react';
import classNames from 'classnames';

/**
 * Dialog Button Actions enums.
 * @type {string}
 */
export const DialogButtonActions = Object.freeze({
    CLOSE: 'CLOSE',
    OPEN: 'OPEN',
    SUCCESS: 'SUCCESS'
});

/**
 * Individual dialog button.
 * @param {string} props.action The dialog button action to invoke.
 */
export const DialogButton = props => (
    <span
        className={'dialog-button'}
        onClick={() => props.onClick(props.action)}>
        {props.label}
    </span>
);

/**
 * Dialog box which overlays onto the screen, blocking input from the
 * rest of the application until dismissed.
 */
export default class Dialog extends React.Component {

    constructor(props) {
        super(props);

        this.onOverlayClick = ::this.onOverlayClick;
        this.onContentClick = ::this.onContentClick;
    }

    /**
     * Event handler when the "greyed out" area is clicked.
     */
    onOverlayClick() {
        if (this.props.escapable) {
            this.props.onAction(DialogButtonActions.CLOSE);
        } else if (this.props.onOverlayClick) {
            this.props.onOverlayClick();
        }
    }

    /**
     * Event handler when content area is clicked.
     */
    onContentClick() {
        if (this.props.onContentClick) {
            this.props.onContentClick();
        }
    }

    render() {
        return (
            <div className='dialog'>
                <div
                    className={classNames('overlay', {
                        'escapable': this.props.escapable === true
                    })}
                    title={this.props.escapable ? 'Click To Close' : ''}
                    onClick={this.onOverlayClick}></div>
                <div
                    className='content'
                    onClick={this.onContentClick}>
                    {React.Children.map(this.props.children, child => {
                        if (child.type === DialogButton) {
                            return React.cloneElement(child, {
                                onClick: this.props.onAction
                            });
                        } else {
                            return child;
                        }
                    })}
                </div>
            </div>
        );
    }
}
