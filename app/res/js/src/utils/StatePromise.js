/**
 * Promisify setState.
 */
export default function setStatePromise (state) {
    return new Promise( resolve => {
        this.setState(state, () => resolve(this.state));
    } );
};
