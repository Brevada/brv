import React from 'react';
import classNames from 'classnames';

/**
 * Simple button.
 */
const Button = props => (
    <button
        className={classNames(props.className || '', 'btn', {
            'submit': props.submit === true,
            'right': props.right === true,
            'left': props.left === true
        })}
        type={props.submit === true ? 'submit' : 'button'}
        onClick={() => {
            if (props.onClick) props.onClick();
        }}
    >{props.label}</button>
);

/**
 * Simple link.
 */
const Link = props => (
    <button
        className={classNames(props.className || '', 'link', {
            'submit': props.submit === true,
            'right': props.right === true,
            'left': props.left === true,
            'danger': props.danger === true
        })}
        type={props.submit === true ? 'submit' : 'button'}
        onClick={() => {
            if (props.onClick) props.onClick();
        }}
    >{props.label}</button>
);

export { Button, Link };
