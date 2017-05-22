import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * Individual header button for use in header controls.
 * @param   {object}     props      React props
 * @param   {boolean}    disable    Disables button
 * @param   {function}   onClick    Handles onClick event
 * @param   {boolean}    negative   If considered a negative action, such as close or cancel
 * @param   {string}     label      Button text
 * @param   {string}     icon       Optional fa icon key
 * @returns {JSX}
 */
const HeaderButton = props => {
    // eslint-disable-next-line require-jsdoc
    const onClick = () => {
        !props.disabled && props.onClick();
    };

    return (
        <div
            className={classNames("btn", "header-btn", {
                disabled: Boolean(props.disabled),
                negative: Boolean(props.negative)
            })}
            onClick={onClick}>
            <span>{props.label}</span>
            { props.icon && (
                <i className={`fa ${props.icon}`}></i>
            ) }
        </div>
    );
};

HeaderButton.propTypes = {
    disabled: PropTypes.bool,
    negative: PropTypes.bool,
    label: PropTypes.string.isRequired,
    icon: PropTypes.string,
    onClick: PropTypes.func
};

HeaderButton.defaultProps = {
    disabled: false,
    negative: false,
    icon: null,
    onClick: () => { /* no op */ }
};

export default HeaderButton;
