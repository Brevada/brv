import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * Individual option within the multi option input.
 * @param   {object} props React props
 * @param   {number} props.value Value type key.
 * @param   {string} props.label Human readable label of the value type option.
 * @param   {function({value_type})} props.onClick Handles submission of rating.
 * @returns {JSX}
 */
const Option = props => {
    const onClick = () => ( // eslint-disable-line require-jsdoc
        props.onClick({ value_type: props.value })
    );

    return (
        <div
            className="value-type"
            onClick={onClick}>
            {props.label}
        </div>
    );
};

Option.propTypes = {
    value: PropTypes.string.isRequired,
    onClick: PropTypes.func,
    label: PropTypes.string.isRequired
};

Option.defaultProps = {
    onClick: () => { /* no op */ }
};

/**
 * MultiOption input.
 * @param   {object} props React props
 * @param   {object} props.valueTypes Key-value pairs of value type keys: labels.
 * @param   {function({value_type})} props.onClick Handles submission of rating.
 * @returns {JSX}
 */
const MultiOption = props => (
    <div className="multi-option-bar">
        <div
            className={classNames("options", {
                binary: Object.keys(props.valueTypes).length === 2,
                unary: Object.keys(props.valueTypes).length === 1
            })}>
            {Object.keys(props.valueTypes).map((typeKey) => (
                <Option
                    key={typeKey}
                    label={props.valueTypes[typeKey]}
                    value={typeKey}
                    onClick={props.onSubmit}
                />
            ))}
        </div>
    </div>
);

MultiOption.propTypes = {
    onSubmit: PropTypes.func,
    valueTypes: PropTypes.object.isRequired
};

MultiOption.defaultProps = {
    onSubmit: () => { /* no op */ }
};

export { Option, MultiOption as default };
