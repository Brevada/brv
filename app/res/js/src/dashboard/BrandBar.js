import React, { Component } from "react";
import PropTypes from "prop-types";

import { DropDownButton, DropDownOption } from "components/DropDownNav";

/**
 * Brand bar
 */
export default class BrandBar extends Component {

    static propTypes = {
        stores: PropTypes.arrayOf(PropTypes.object).isRequired,
        storeId: PropTypes.number.isRequired,
        onStoreChange: PropTypes.func
    };

    static defaultProps = {
        onStoreChange: () => { /* no op */ }
    };

    /**
     * @constructor
     */
    constructor(){
        super();
    }

    /**
     * Navigates to settings page.
     * @returns {void}
     */
    static redirectSettings() {
        window.location.replace("/settings");
    }

    /**
     * Navigates to logout.
     * @returns {void}
     */
    static redirectLogout() {
        window.location.replace("/logout");
    }

    /**
     * @override
     */
    render() {
        return (
            <div className="brand-bar">
                <div className="brand logo-lq logo"></div>
                <DropDownButton
                    className="account"
                    label={"Account"}>
                    <DropDownOption
                        label={"Settings"}
                        onClick={this.constructor.redirectSettings}
                    />
                    <DropDownOption
                        label={"Logout"}
                        onClick={this.constructor.redirectLogout}
                    />
                </DropDownButton>
                { this.props.stores && this.props.stores.length > 1 &&
                    <DropDownButton className="stores" label={"Change Store"}>
                        {this.props.stores.map(store => (
                            <DropDownOption
                                label={store.name}
                                key={store.id}
                                active={store.id === this.props.storeId}
                                onClick={() => { // eslint-disable-line react/jsx-no-bind
                                    if (store.id !== this.props.storeId) {
                                        this.props.onStoreChange(store.id);
                                    }
                                }}
                            />
                        ))}
                    </DropDownButton>
                 }
            </div>
        );
    }
}
