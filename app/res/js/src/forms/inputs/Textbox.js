import React from 'react';
import classNames from 'classnames';
import AbstractTextInput from 'forms/AbstractTextInput';

/**
 * A basic textbox.
 */
export default class Textbox extends AbstractTextInput {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div
                className={classNames('input', 'input-textbox', {
                    attempted: this.state.attempted,
                    always: !!this.props.requireAlways
                })}
                {...(this.getContainerProps())}>
                <input
                    type={this.props.type || 'text'}
                    {...(this.getInternalProps())}
                />
                { this.getPlaceholder() }
            </div>
        );
    }
}
