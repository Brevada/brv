import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

import Input from "forms/Input";
import { Textbox } from "forms/Form";

/**
 * Preset domain option for email input.
 * @param {object} props React props
 * @returns {JSX}
 */
const DomainOption = props => {
    /**
     * Handles onClick for Domain Option.
     * @returns {void}
     */
    const onClick = () => {
        props.onClick(props.index);
    };

    const classes = classNames("domain-option", {
        "active": props.active
    });

    return (
        <div
            className={classes}
            onClick={onClick}>
            <span>
                { props.index > -1 && (<span className="at">{"@"}</span>)}
                {props.value}
            </span>
        </div>
    );
};

DomainOption.propTypes = {
    index: PropTypes.number.isRequired,
    onClick: PropTypes.func.isRequired,
    active: PropTypes.bool,
    value: PropTypes.string.isRequired
};

DomainOption.defaultProps = {
    active: false
};

/**
 * Form input for emails with "smart" domain selection.
 */
export default class IntelliEmailInput extends Input {

    /**
     * @constructor
     * @param  {object} props React props
     */
    constructor(props) {
        super(props);

        this.state = {

            /* Fully selected email, e.g. smith@example.com */
            value: "",

            /* User inputted detail. e.g. smith in smith@example.com */
            mailbox: "",

            /* The selected domain's index, or -1 if other. */
            domain: 0,

            /* The possible domains to choose from. */
            domains: props.domains || []
        };

        this.onDomainSelect = ::this.onDomainSelect;
        this.onMailboxChange = ::this.onMailboxChange;
    }

    /**
     * @override
     */
    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Handles email domain change event.
     * @param   {number} domainI Domain option index
     * @returns {void}
     */
    onDomainSelect(domainI) {
        this.setState(state => {
            if (domainI === -1 && this._mailbox && this._mailbox.onFocus) {
                this._mailbox.onFocus();
            }

            if (domainI === -1 && this.state.domain === -1) {
                // Toggle back to presets mode.
                domainI = 0;

                // Remove @...
                if (state.mailbox && state.mailbox.indexOf("@") >= 0) {
                    return {
                        domain: domainI,
                        mailbox: state.mailbox.substring(0, state.mailbox.indexOf("@")),
                        value: state.mailbox.substring(0, state.mailbox.indexOf("@")) +
                                "@" + this.state.domains[domainI]
                    };
                }
            }

            return {
                domain: domainI,
                value: this.state.mailbox + "@" + this.state.domains[domainI]
            };
        }, () => {
            if (this._mailbox && this._mailbox.onChange) {
                this._mailbox.onChange({ value: this.state.mailbox });
            }
        });
    }

    /**
     * Handles mailbox (user input pre @) change event.
     * @param   {string} mailbox The mailbox identifier
     * @returns {void}
     */
    onMailboxChange(mailbox) {
        if (mailbox === this.state.mailbox) return;

        this.setState({
            mailbox,
            value: mailbox + (
                this.state.domain === -1 ?
                "" :
                "@" + this.state.domains[this.state.domain]
            )
        });
    }

    /**
     * @override
     */
    render() {
        const divClass = classNames("intelli-email-input", {
            "other": this.state.domain === -1
        });

        /**
         * Saves reference to input element.
         * @param   {DOMElement} txt Element to save.
         * @returns {void}
         */
        const saveInput = txt => {
            this._mailbox = txt;
        };

        return (
            <div
                className={divClass}>
                <Textbox
                    className="mailbox"
                    name="mailbox"
                    seamless={true}
                    placeHolder={this.state.domain === -1 ? "full email address" : "email name"}
                    onChange={this.onMailboxChange}
                    props={{
                        autoFocus: true,
                        autoComplete: "off"
                    }}
                    input={saveInput}
                />
                <input
                    type="hidden"
                    value={this.state.value || ""}
                    name="email"
                />
                <span className="at">{"@"}</span>
                <div className="domains">
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
                        value={this.state.domain === -1 ? "back to presets" : "other"}
                        onClick={this.onDomainSelect}
                        active={this.state.domain === -1}
                    />
                </div>
            </div>
        );
    }
}
