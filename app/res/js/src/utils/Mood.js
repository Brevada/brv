/* eslint-disable no-magic-numbers */

/**
 * Gets the mood CSS class corresponding to a value within a range.
 *
 * @param {number} val The value to lookup the mood for.
 * @param {number} [min=0] The minimum possible value of val.
 * @param {number} [max=100] The maximum possible value of val.
 * @returns {string}
 */
const mood = (val, min = 0, max = 100) => {
    return "mood-" + (Math.round((parseFloat(val) + Math.abs(min)) * 100 / (max - min) / 10) * 10);
};

export { mood };
