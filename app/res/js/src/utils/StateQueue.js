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
 * @param {object} self
 * @param {function} predicate
 */
function StateQueue(self, predicate) {
    /* Force factory pattern. Guaranteeing new "this". */
    return new function _stateQueue(){
        /* Stores queue of tasks. */
        let _queue = [];

        /* Stores index of next task to execute. */
        let _pointer = 0;

        /* Stores return value of previously executed task. */
        let _result = undefined;

        /* Callback to invoke upon completion of queue. */
        let _complete = () => false;

        /**
         * Loads a wait onto the queue. Waits for a specified number of
         * seconds.
         *
         * @param  {number} seconds The number of seconds to wait.
         * @return {self}
         */
        this.wait = seconds => {
            _queue.push({
                type: 'wait',
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
         * @param  {function|object} obj
         * @return {self}
         */
        this.do = obj => {
            _queue.push({
                type: 'do',
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
         * @return {self}
         */
        this.exec = (complete) => {
            /* Save complete if supplied. */
            _complete = _complete || complete;

            if (_queue.length === 0) return;

            if (_pointer >= _queue.length) {
                /* If complete, invoke complete callback. */
                this.reset();
                if (predicate() && _complete) {
                    _complete.call(self, _result);
                }
                return;
            }

            /* If queue should not advance. */
            if (!predicate()) return;

            /* Load the task to execute, while advancing the pointer. */
            let task = _queue[_pointer++];
            if (task.type !== 'wait' && task.type !== 'do') {
                throw new Error('Invalid task type.');
            }

            if (task.type === 'wait') {
                /* Resume upon completion of timeout. */
                setTimeout(::this.exec, task.data);
            }

            if (task.type === 'do') {
                if (typeof task.data === 'function') {
                    /* Execute function, store result for next 'do' task. */
                    _result = task.data(_result);
                    this.exec();
                } else if (typeof task.data === 'object') {
                    _result = undefined;
                    self.setState && self.setState(task.data, ::this.exec);
                } else {
                    throw new Error('Invalid task do parameter.');
                }
            }
        };

        /**
         * Resets pointer and result.
         */
        this.reset = () => {
            _pointer = 0;
            _result = undefined;
            return this;
        };
    };
}

export default StateQueue;
