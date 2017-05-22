/**
 * StateQueue
 *
 * Enables easy chaining of delayed tasks for use with React states.
 *
 * @example
 * StateQueue(this, () => environment == good)
 * .do({ step: 1 })
 * .wait(500)
 * .do(() => 5)
 * .do(x => x+1)
 * .do(x => ({ step: x }))
 * .exec()
 * // step is 6.
 *
 * @param   {object} self The context in which to call StateQueue.
 * @param   {function} predicate A predicate to test before every step, if the
 *                               predicate fails, abort.
 * @returns {StateQueue}
 */
function StateQueue(self, predicate) {

    /* Force factory pattern. Guaranteeing new "this". */
    return new function _stateQueue(){
        /* eslint-disable no-invalid-this */

        /* Stores queue of tasks. */
        const _queue = [];

        /* Stores index of next task to execute. */
        let _pointer = 0;

        /* Stores return value of previously executed task. */
        let _result = null;

        /* Callback to invoke upon completion of queue. */
        let _complete = () => false; // eslint-disable-line require-jsdoc

        /**
         * Executes a task from the queue.
         *
         * @param   {string} type The type of task to execute.
         * @param   {object|function} data The data to perform the task on.
         * @returns {void}
         */
        const _executeTask = ({type, data}) => {
            /* Resume upon completion of timeout. */
            if (type === "wait") setTimeout(::this.exec, data);

            if (type === "do") {
                (({
                    function: () => {
                        /* Execute function, store result for next 'do' task. */
                        _result = data(_result);
                        this.exec();
                    },
                    object: () => {
                        _result = null;
                        self.setState && self.setState(data, ::this.exec);
                    }
                })[typeof data])();
            }
        };

        /**
         * Loads a wait onto the queue. Waits for a specified number of
         * seconds.
         *
         * @param   {number} seconds The number of seconds to wait.
         * @returns {self}
         */
        this.wait = seconds => {
            _queue.push({
                type: "wait",
                data: seconds
            });

            return this;
        };

        /**
         * Loads a set state or "runnable" task onto the queue. If an
         * object is supplied, self.setState will be invoked with the object.
         * If a function is supplied, the function will be invoked with the
         * previously executed task's return value.
         *
         * @param   {function|object} obj Parameter to pass to setState.
         * @returns {self}
         */
        this.do = obj => {
            _queue.push({
                type: "do",
                data: obj
            });

            return this;
        };

        /**
         * Executes the next task in the queue, advancing the queue.
         *
         * There is no guarantee that the exec will be returned in order
         * of queue execution, due to the use of async timeouts and
         * React.setState callbacks.
         *
         * @param   {function} complete Completion callback.
         * @returns {void}
         */
        this.exec = (complete) => {
            /* Save complete if supplied. */
            _complete = _complete || complete;

            const predTest = _queue.length && predicate();

            if (predTest && _pointer >= _queue.length && _complete) {
                /* If complete, invoke complete callback. */
                this.reset();
                _complete.call(self, _result);
            } else if (predTest) {
                /* Load the task to execute, while advancing the pointer. */
                _executeTask(_queue[_pointer++]);
            }
        };

        /**
         * Resets pointer and result.
         * @returns {void}
         */
        this.reset = () => {
            _pointer = 0; // eslint-disable-line no-magic-numbers
            _result = null;

            return this;
        };

        /* eslint-enable no-invalid-this */
    }();
}

export default StateQueue;
