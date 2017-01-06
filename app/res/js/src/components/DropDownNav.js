import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';

class DropDownButton extends React.Component {
    constructor() {
        super();

        this.state = {
            open: false
        };

        this.toggle = this.toggle.bind(this);
        this.close = this.close.bind(this);
    }

    toggle() {
        this.setState({ open: !this.state.open });
    }

    close() {
        this.setState({ open: false });
    }

    render() {
        let classes = classNames({
            'dropdown-btn': true,
            'open': this.state.open
        });

        return (
            <div
                className={classes}
                onClick={this.toggle}
                onBlur={this.close}
                tabIndex={0}
            >
                <div className='label'>
                    {this.props.label}
                    <i className='fa fa-chevron-down'></i>
                </div>
                <div className='options'>{this.props.children}</div>
            </div>
        );
    }
}

const DropDownOption = props => (
    <div
        className='option'
        onClick={props.onClick}
    >{props.label}</div>
);

export {DropDownButton, DropDownOption};
