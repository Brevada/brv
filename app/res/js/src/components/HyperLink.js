import React from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

/**
 * A typical hyperlink.
 *
 * @param   {object} props Properties.
 * @param   {string} props.label The display text.
 * @param   {function(Event)} props.onClick The onClick event handler.
 * @param   {boolean} props.left Option to float left.
 * @param   {boolean} props.right Option to float right.
 * @param   {boolean} props.btnLike Option to appear as a button, rather than a link.
 * @param   {boolean} props.danger Is the button's action negative.
 * @param   {string} props.href Optional page to navigate to using default href behaviour.
 * @returns {JSX}
 */
const HyperLink = props => {
    /**
     * Triggers props.onClick if defined.
     * @param  {Event} e onClick event.
     * @returns {void}
     */
    const onClick = e => {
        if (props.onClick && !props.href) {
            e.preventDefault();
            props.onClick(e);
        }
    };

    return (
        <a
            className={classNames("link", {
                left: Boolean(props.left),
                right: Boolean(props.right),
                "btn-like": Boolean(props.btnLike),
                "danger": Boolean(props.danger)
            })}
            href={props.href || "#"}
            {...((props.target && { target: props.target }) || {})}
            {...({ onClick } || {})}>
            {props.label}
        </a>
    );
};

HyperLink.propTypes = {
    left: PropTypes.bool,
    right: PropTypes.bool,
    btnLike: PropTypes.bool,
    danger: PropTypes.bool,
    label: PropTypes.string.isRequired,
    onClick: PropTypes.func,
    href: PropTypes.string,
    target: PropTypes.string
};

HyperLink.defaultProps = {
    left: false,
    right: false,
    btnLike: false,
    danger: false,
    href: null,
    onClick: null,
    target: null
};

export { HyperLink };
