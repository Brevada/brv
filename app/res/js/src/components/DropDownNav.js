import React from 'react';
import classNames from 'classnames';

/**
 * A single option in the DropDownButton.
 *
 * @param {object} props
 * @param {string} props.label The display text.
 * @param {function(Event)} props.onClick The onClick event handler.
 */
const DropDownOption = props => (
    <div
        className='option'
        onClick={props.onClick}>
        {props.label}
    </div>
);

/**
 * A navigation button capable of displaying a dropdown of options.
 */
class DropDownButton extends React.Component {
    constructor() {
        super();

        this.state = {
            open: false
        };

        this.toggle = ::this.toggle;
        this.close = ::this.close;
    }

    /**
     * Toggles the dropdown options' visiblity.
     */
    toggle() {
        this.setState(s => ({ open: !s.open }));
    }

    /**
     * Closes the dropdown box.
     */
    close() {
        this.setState({ open: false });
    }

    render() {
        return (
            <div
                className={classNames('dropdown-btn', {
                    'open': this.state.open
                })}
                onClick={this.toggle}
                onBlur={this.close}
                tabIndex={0}>
                <div className='label'>
                    {this.props.label}
                    <i className='fa fa-chevron-down'></i>
                </div>
                <div className='options'>{this.props.children}</div>
            </div>
        );
    }
}

export { DropDownButton, DropDownOption };
