import axios from "axios";

/**
 * Gets the ajax function to use for client-server communication.
 * @returns {function}
 */
export default function ajax(...args) {
    /* If defined, use interceptor to allow offline functionality in offline modes. */
    const handler = window.brv && window.brv.feedback ?
                 window.brv.feedback.interceptor || axios :
                 axios;

    return handler(...args);
}
