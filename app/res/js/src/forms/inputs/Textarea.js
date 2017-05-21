import React from "react";
import classNames from "classnames";
import AbstractTextInput from "forms/AbstractTextInput";

/**
 * A basic textarea.
 */
export default class Textarea extends AbstractTextInput {

    /**
     * @constructor
     * @param {object} props React props
     */
    constructor(props) {
        super(props);
    }

    /**
     * @override
     */
    render() {
        return (
            <div
                className={classNames("input", "input-textarea", {
                    attempted: this.state.attempted,
                    always: Boolean(this.props.requireAlways)
                })}
                {...(this.getContainerProps())}>
                <textarea
                    {...(this.getInternalProps())}
                />
                { this.getPlaceholder() }
            </div>
        );
    }
}
