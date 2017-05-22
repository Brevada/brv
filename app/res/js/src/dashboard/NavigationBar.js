import React, { Component } from "react";
import PropTypes from "prop-types";

import { NavigationButton } from "components/NavigationButton";
import { HyperLink } from "components/HyperLink";
import BrandBar from "dashboard/BrandBar";

/**
 * Main dashboard navigation bar. Also contains top brand bar.
 */
export default class NavigationBar extends Component {
    static propTypes = {
        onStoreChange: PropTypes.func,
        onChangeView: PropTypes.func.isRequired,
        stores: PropTypes.arrayOf(PropTypes.object).isRequired,
        storeId: PropTypes.number.isRequired,
        view: PropTypes.string.isRequired,
        url: PropTypes.string
    };

    static defaultProps = {
        onStoreChange: () => { /* no op */ },
        onChangeView: () => { /* no op */ },
        url: ""
    };

    /**
     * @constructor
     */
    constructor() {
        super();
    }

    /**
     * @override
     */
    render() {
        return (
            <div className="navigation-bar">
                <BrandBar
                    onStoreChange={this.props.onStoreChange}
                    stores={this.props.stores}
                    storeId={this.props.storeId}
                />
                <div className="view-navbar">
                    <NavigationButton
                        label={"Your Aspects"}
                        value={"ASPECTS"}
                        onClick={this.props.onChangeView}
                        active={this.props.view === "ASPECTS"}
                    />
                    <NavigationButton
                        label={"Events"}
                        value={"EVENTS"}
                        onClick={this.props.onChangeView}
                        active={this.props.view === "EVENTS"}
                    />
                    {this.props.url &&
                        <HyperLink
                            label={`brevada.com/${this.props.url}`}
                            href={`/${this.props.url}`}
                            target="_blank"
                        />
                    }
                </div>
            </div>
        );
    }

}
