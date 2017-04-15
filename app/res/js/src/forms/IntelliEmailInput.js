import React from 'react';
import ReactDOM from 'react-dom';

import { Input, Textbox } from 'forms/Form';

/**
 * Preset domain option for email input.
 */
const DomainOption = props => (
    <div
        className={'domain-option'+(props.active ? ' active' : '')}
        onClick={() => props.onClick(props.index)}>
        <span>
            { props.index > -1 && (<span className='at'>@</span>)}
            {props.value}
        </span>
    </div>
);

/**
 * Form input for emails with "smart" domain selection.
 */
export default class IntelliEmailInput extends Input {
    constructor(props) {
        super(props);

        this.state = {
            /* Fully selected email, e.g. smith@example.com */
            value: '',

            /* User inputted detail. e.g. smith in smith@example.com */
            mailbox: '',

            /* The selected domain's index, or -1 if other. */
            domain: 0,

            /* The possible domains to choose from. */
            domains: props.domains || []
        };

        this.onDomainSelect = ::this.onDomainSelect;
        this.onMailboxChange = ::this.onMailboxChange;
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Handles email domain change event.
     */
    onDomainSelect(domainI) {
        this.setState(state => {
            if (domainI === -1) {
                if (this._mailbox && this._mailbox.onFocus) {
                    this._mailbox.onFocus();
                }
            }

            if (domainI === -1 && this.state.domain === -1) {
                // Toggle back to presets mode.
                domainI = 0;

                // Remove @...
                if (state.mailbox && state.mailbox.indexOf('@') >= 0) {
                    return {
                        domain: domainI,
                        mailbox: state.mailbox.substring(0, state.mailbox.indexOf('@')),
                        value: state.mailbox.substring(0, state.mailbox.indexOf('@')) + '@' + this.state.domains[domainI]
                    };
                }
            }

            return {
                domain: domainI,
                value: this.state.mailbox + '@' + this.state.domains[domainI]
            };
        }, () => {
            if (this._mailbox && this._mailbox.onChange) {
                this._mailbox.onChange({ value: this.state.mailbox });
            }
        });
    }

    /**
     * Handles mailbox (user input pre @) change event.
     */
    onMailboxChange(mailbox) {
        if (mailbox === this.state.mailbox) return;

        this.setState({
            mailbox: mailbox,
            value: mailbox + (
                this.state.domain === -1 ?
                '' :
                '@' + this.state.domains[this.state.domain]
            )
        });
    }

    render() {
        return (
            <div className={'intelli-email-input' + (this.state.domain === -1 ? ' other' : '')}>
                <Textbox
                    className='mailbox'
                    name='mailbox'
                    seamless={true}
                    placeHolder={this.state.domain === -1 ? 'full email address' : 'email name'}
                    onChange={this.onMailboxChange}
                    props={{
                        autoFocus: true,
                        autoComplete: "off"
                    }}
                    input={txt => this._mailbox = txt}
                />
                <input
                    type='hidden'
                    value={this.state.value || ''}
                    name='email'
                />
                <span className='at'>@</span>
                <div className='domains'>
                    { this.state.domain !== -1 && this.state.domains.map((domain, i) => (
                        <DomainOption
                            index={i}
                            key={i}
                            value={domain}
                            onClick={this.onDomainSelect}
                            active={i === this.state.domain}
                        />
                    )) }
                    <DomainOption
                        index={-1}
                        value={this.state.domain === -1 ? 'back to presets' : 'other'}
                        onClick={this.onDomainSelect}
                        active={-1 === this.state.domain}
                    />
                </div>
            </div>
        );
    }
}
