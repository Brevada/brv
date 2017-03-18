import React from 'react';
import classNames from 'classnames';

/**
 * A typical hyperlink.
 *
 * @param {object} props
 * @param {string} props.label The display text.
 * @param {function(Event)} props.onClick The onClick event handler.
 * @param {boolean} props.left Option to float left.
 * @param {boolean} props.right Option to float right.
 * @param {boolean} props.btnLike Option to appear as a button, rather than a link.
 * @param {boolean} props.danger Is the button's action negative?
 */
const Link = props => (
    <a
        className={classNames('link', {
            left: props.left === true,
            right: props.right === true,
            'btn-like': props.btnLike === true,
            'danger': props.danger === true
        })}
        href='#'
        onClick={e => {
            if (props.onClick) {
                e.preventDefault();
                props.onClick(e);
            }
        }}>
        {props.label}
    </a>
);

export { Link };
