import React from 'react';

const Dialog = props => (
    <div className={'dialog-overlay ' + (props.className || '')}>
        <div className='dialog-content'>
            {props.children}
        </div>
    </div>
);

export default Dialog;
