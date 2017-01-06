import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';

const Link = props => (
    <a
        className={classNames('link', {
            left: props.left === true,
            right: props.right === true,
            'btn-like': props.btnLike === true
        })}
        href={'#'}
        onClick={(e) => {
            e.preventDefault();
            if (props.onClick) {
                props.onClick(e);
            }
        }}
    >{props.label}</a>
);

export { Link };
