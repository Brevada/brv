import React, { Component } from "react";
import PropTypes from "prop-types";

/**
 * Generic input type.
 */
export default class ResponseInput extends Component {
    static propTypes = {
        input: PropTypes.oneOfType([
            PropTypes.node,
            PropTypes.element,
            PropTypes.func
        ]).isRequired
    };

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
        const Input = this.props.input;

        return (
            <div>
                <Input
                    {...this.props}
                />
            </div>
        );
    }
}
