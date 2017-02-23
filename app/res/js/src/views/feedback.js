import React from 'react';
import ReactDOM from 'react-dom';
import docReady from 'doc-ready';

import DataLayer from 'forms/DataLayer';
import Loader from 'dashboard/Loader';
import Feedback from 'feedback/Feedback';

/**
 * Connects feedback with data layer.
 *
 * @param {object} props
 * @param {object} props.data Data passed from data layer.
 * @param {number} props.data.id The store id.
 * @param {boolean} props.loading Whether the store id is still being retrieved.
 */
const FeedbackLinked = props => {
    if (!props.data.id || props.loading) {
        return (
            <Loader
                className='view'
                messages={[
                    "Updating your system..."
                ]}
            />
        );
    } else {
        return (<Feedback {...props} storeId={props.data.id} />);
    }
};

docReady(function() {
    ReactDOM.render(
        (<DataLayer action="/api/feedback/config" data={{ id: window.brv.feedback.id }}>
            <FeedbackLinked />
        </DataLayer>),
        document.getElementById('feedback-root')
    );
});
