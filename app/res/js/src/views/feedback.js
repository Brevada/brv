import React from "react";
import PropTypes from "prop-types";
import ReactDOM from "react-dom";
import docReady from "doc-ready";

/* Fixes 300ms tap delay. */
import initReactFastclick from "react-fastclick";
initReactFastclick();

import Fetch from "forms/Fetch";
import Loader from "dashboard/Loader";
import Feedback from "feedback/Feedback";

/**
 * Connects feedback with data layer.
 *
 * @param {object} props Properties.
 * @param {object} props.data Data passed from data layer.
 * @param {number} props.data.id The store id.
 * @param {boolean} props.loading Whether the store id is still being retrieved.
 * @returns {JSX}
 */
const FetchedFeedback = props => {
    if (!props.data.id || props.loading) {
        return (
            <Loader
                className="view"
                messages={[
                    "Loading..."
                ]}
            />
        );
    } else {
        return (<Feedback {...props} storeId={props.data.id} />);
    }
};

FetchedFeedback.propTypes = {
    data: PropTypes.object,
    loading: PropTypes.bool
};

FetchedFeedback.defaultProps = {
    data: {},
    loading: true
};

docReady(() => {
    /* global brv */
    brv.feedback.scroll.monitor({
        key: "header",
        px: 10,
        remainder: 140 /* Max header height minus smallest height. */
    });

    ReactDOM.render(
        <Fetch
            action="/api/feedback/config"
            data={{ id: brv.feedback.id() || false }}
            readCache={brv.feedback.getConfig}
            writeCache={brv.feedback.saveConfig}>
            <FetchedFeedback />
        </Fetch>,
        document.getElementById("feedback-root"),
        () => {
            brv.env && brv.env.fireReady && brv.env.fireReady();
        }
    );
});
