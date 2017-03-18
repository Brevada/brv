import React from 'react';
import ReactDOM from 'react-dom';
import docReady from 'doc-ready';

import Login from 'login/Login';

docReady(function() {
    ReactDOM.render(
        <Login />,
        document.getElementById('login-root')
    );
});
