import React from "react";
import PropTypes from "prop-types";

/**
 * Generic error message holder to be used by a Form.
 * @param   {object} props React props.
 * @param   {string} props.text The error message to display.
 * @returns {JSX}
 */
const ErrorMessage = props => (
    <div className="form-error">{props.text}</div>
);

ErrorMessage.propTypes = {
    text: PropTypes.string
};

ErrorMessage.defaultProps = {
    text: ""
};

export default ErrorMessage;
