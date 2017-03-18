/**
 * Gets the mood CSS class corresponding to a value within a range.
 *
 * @param {number} val
 * @param {number} [min=0]
 * @param {number} [max=100]
 * @return {string}
 */
const Mood = (val, min=0, max=100) => {
    return 'mood-' + (Math.round(((parseFloat(val) + Math.abs(min)) * 100 / (max-min))/10)*10);
};

export { Mood };
