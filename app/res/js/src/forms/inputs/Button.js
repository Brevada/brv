import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * Simple button.
 * @param   {object} props React props
 * @returns {JSX}
 */
const Button = props => {
    /**
     * Handles button's onClick event.
     * @returns {void}
     */
    const onClick = () => {
        if (props.onClick) props.onClick();
    };

    const btnClasses = classNames(
        props.className || "",
        props.link ? "link" : "btn",
        {
            "submit": Boolean(props.submit),
            "right": Boolean(props.right),
            "left": Boolean(props.left),
            "danger": Boolean(props.danger)
        }
    );

    return (
        <button
            className={btnClasses}
            type={props.submit ? "submit" : "button"}
            onClick={onClick}>
            {props.label}
        </button>
    );
};

Button.propTypes = {
    label: PropTypes.string.isRequired,
    onClick: PropTypes.func,
    submit: PropTypes.bool,
    right: PropTypes.bool,
    left: PropTypes.bool,
    danger: PropTypes.bool,
    className: PropTypes.string,
    link: PropTypes.bool
};

Button.defaultProps = {
    className: "",
    submit: false,
    right: false,
    left: false,
    danger: false,
    link: false,
    onClick: null
};

/**
 * Simple link.
 * @param   {object} props React props
 * @returns {JSX}
 */
const Link = props => (
    <Button
        link={true}
        {...props}
    />
);

export { Button, Link };
