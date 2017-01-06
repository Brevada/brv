import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';

export const DialogButtonActions = Object.freeze({
    CLOSE: 'CLOSE',
    OPEN: 'OPEN',
    SUCCESS: 'SUCCESS'
});

export const DialogButton = props => (
    <span
        className={'dialog-button'}
        onClick={() => {
            props.onClick(props.action);
        }}
    >
        {props.label}
    </span>
);

export default class Dialog extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div className='dialog'>
                <div className='overlay'></div>
                <div className='content'>
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
