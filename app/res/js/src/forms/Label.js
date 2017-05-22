import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * A form label.
 * @param   {object} props React props.
 * @param   {string} props.text The display text.
 * @param   {boolean} props.inline Set display style to inline.
 * @param   {function} props.onClick On click event handler.
 * @returns {JSX}
 */
const Label = props => {
    const labelClass = classNames("label", {
        inline: Boolean(props.inline)
    });

    return (
        <label
            className={labelClass}
            onClick={props.onClick}>
            {props.text}
        </label>
    );
};

Label.propTypes = {
    inline: PropTypes.bool,
    onClick: PropTypes.func,
    text: PropTypes.string.isRequired
};

Label.defaultProps = {
    inline: false,
    onClick: () => { /* no op */ }
};

export default Label;
