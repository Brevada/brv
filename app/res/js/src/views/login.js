require('expose?$!expose?jQuery!jquery');

import React from 'react';
import ReactDOM from 'react-dom';

import Login from 'login/Login';

$(function() {
    ReactDOM.render(
        <Login />,
        document.getElementById('login-root')
    );
});
