import React from "react";
import PropTypes from "prop-types";

/**
 * Individual rating within the rating bar.
 * @param   {object} props React props
 * @param   {number} props.ordinal The rating's ordinal value, rather than percent.
 * @param   {number} props.value Rating's percent value.
 * @param   {function(number, number)} props.onClick Handles submission of rating.
 * @returns {JSX}
 */
const Rating = props => {
    const onClick = () => ( // eslint-disable-line require-jsdoc
        props.onClick(props.value, props.ordinal)
    );

    return (
        <div
            className={`rating rating-${props.ordinal}`}
            onClick={onClick}>
        </div>
    );
};

Rating.propTypes = {
    value: PropTypes.number.isRequired,
    ordinal: PropTypes.number.isRequired,
    onClick: PropTypes.func
};

Rating.defaultProps = {
    onClick: () => { /* no op */ }
};

/**
 * 5 star rating bar.
 * @param   {object} props React props
 * @param   {function(number, number)} props.onClick Handles submission of rating.
 * @returns {JSX}
 */
const RatingBar = props => (
    <div className="rating-bar">
        <div className="ratings">
            {[...Array(5)].map((x, i) => (
                <Rating
                    key={i}
                    value={(i + 1) * 20}
                    ordinal={i}
                    onClick={props.onSubmit}
                />
            ))}
        </div>
        <div className="ly ly-float hint">
            <span className="left">{"Worst"}</span>
            <span className="right">{"Best"}</span>
        </div>
    </div>
);

RatingBar.propTypes = {
    onSubmit: PropTypes.func
};

RatingBar.defaultProps = {
    onSubmit: () => { /* no op */ }
};

export { Rating, RatingBar as default };
