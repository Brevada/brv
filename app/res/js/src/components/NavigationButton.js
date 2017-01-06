import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';

const NavigationButton = props => {
    let classes = classNames({
        'navigation-btn': true,
        'active': props.active
    });

    return (
        <div
            className={classes}
            onClick={() => {
                props.onClick(props.view);
            }}
        >{props.label}</div>
    );
};

export {NavigationButton};
