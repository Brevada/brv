import React from 'react';
import classNames from 'classnames';

/**
 * A navigation button used mainly to switch between views.
 *
 * @param {object} props React props.
 * @param {boolean} props.active Indicates whether the button is selected.
 * @param {string} props.label The display text of the button.
 * @param {function(object)} props.onClick Callback invoked upon button click.
 */
const NavigationButton = props => (
    <div
        className={classNames('navigation-btn', {
            'active': props.active
        })}
        onClick={() => props.onClick && props.onClick(props.view)}>
        {props.label}
    </div>
);

export { NavigationButton };
