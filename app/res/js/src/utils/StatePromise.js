/**
 * Promisify setState.
 * @param {object|function} state The new state.
 * @returns {Promise}
 */
export default function setStatePromise (state) {
    return new Promise( (resolve, reject) => {
        /* eslint-disable no-invalid-this */
        if (this._unmounted) reject();
        this.setState(state, () => resolve(this.state));
        /* eslint-enable no-invalid-this */
    } );
}
