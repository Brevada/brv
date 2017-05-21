import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

import Input from "forms/Input";
import { Swipeable, defineSwipe } from "react-touch";

/**
 * Toggle input.
 */
export default class Toggle extends Input {
    static propTypes = {
        default: PropTypes.bool,
        inverted: PropTypes.bool,
        positiveLabel: PropTypes.string,
        negativeLabel: PropTypes.string,
        name: PropTypes.string.isRequired
    };

    static defaultProps = {
        default: false,
        inverted: false,
        positiveLabel: "Yes",
        negativeLabel: "No"
    };

    /**
     * @constructor
     * @param  {object} props React props.
     */
    constructor(props) {
        super(props);

        this.state = {
            checked: props.default
        };

        this.onClickLeft = ::this.onClickLeft;
        this.onClickRight = ::this.onClickRight;
    }

    /**
     * Handles left side clicked event.
     * @returns {void}
     */
    onClickLeft() {
        this.setState({
            checked: !this.props.inverted
        });
    }

    /**
     * Handles right side clicked event.
     * @returns {void}
     */
    onClickRight() {
        this.setState({
            checked: this.props.inverted
        });
    }

    /**
     * Gets the left label text.
     * @returns {string}
     */
    getLeftLabel() {
        if (this.props.inverted) {
            return this.props.negativeLabel;
        }

        return this.props.positiveLabel;
    }

    /**
     * Gets the right label text.
     * @returns {string}
     */
    getRightLabel() {
        if (this.props.inverted) {
            return this.props.positiveLabel;
        }

        return this.props.negativeLabel;
    }

    /**
     * @override
     */
    render() {
        const swipe = defineSwipe({ swipeDistance: 50 });
        const toggleClass = classNames({
            "positive": this.state.checked,
            "negative": !this.state.checked,
            "inverted": this.props.inverted
        });

        return (
            <Swipeable
                config={swipe}
                onSwipeLeft={this.onClickLeft}
                onSwipeRight={this.onClickRight}>
                <div className="toggle-input">
                    <div className={toggleClass}>
                        <div className="slider"></div>
                        <div className="left" onClick={this.onClickLeft}>
                            {this.getLeftLabel()}
                        </div>
                        <div className="right" onClick={this.onClickRight}>
                            {this.getRightLabel()}
                        </div>
                    </div>
                    <input
                        type="hidden"
                        value={this.state.checked.toString()}
                        name={this.props.name}
                    />
                </div>
            </Swipeable>
        );
    }
}
