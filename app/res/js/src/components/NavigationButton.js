import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * A navigation button used mainly to switch between views.
 *
 * @param {object} props React props.
 * @param {boolean} props.active Indicates whether the button is selected.
 * @param {string} props.label The display text of the button.
 * @param {string} props.value The value to pass to the onClick handler.
 * @param {function(object)} props.onClick Callback invoked upon button click.
 *
 * @returns {JSX}
 */
const NavigationButton = props => {
    /**
     * Triggers props.onClick.
     * @param   {Event} e The onClick event.
     * @returns {void}
     */
    const onClick = e => {
        if (props.onClick) {
            e.preventDefault();
            props.onClick(props.value);
        }
    };

    return (
        <div
            className={classNames("navigation-btn", {
                "active": props.active
            })}
            onClick={onClick}>
            {props.label}
        </div>
    );
};

NavigationButton.propTypes = {
    active: PropTypes.bool,
    onClick: PropTypes.func.isRequired,
    value: PropTypes.string.isRequired,
    label: PropTypes.string.isRequired
};

NavigationButton.defaultProps = {
    active: false
};

export { NavigationButton };
