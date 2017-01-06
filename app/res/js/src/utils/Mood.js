const Mood = (val, min=0, max=100) => {
    return 'mood-' + (Math.round(((parseFloat(val) + Math.abs(min)) * 100 / (max-min))/10)*10);
};

const MoodColor = (mood) => {
    return '#ff00ff';
};

export { Mood, MoodColor };
