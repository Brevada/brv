import React from 'react';

/**
 * Individual rating within the rating bar.
 */
const Rating = props => (
    <div
        className={`rating rating-${props.ordinal}`}
        onClick={() => props.onClick(props.value, props.ordinal)}>
    </div>
);

/**
 * 5 star rating bar.
 */
const RatingBar = props => (
    <div className='rating-bar'>
        <div className='ratings'>
            {[...Array(5)].map((x, i) =>
                <Rating
                    key={i}
                    value={(i+1)*20}
                    ordinal={i}
                    onClick={props.onSubmit}
                />
            )}
        </div>
        <div className='ly ly-float hint'>
            <span className='left'>Worst</span>
            <span className='right'>Best</span>
        </div>
    </div>
);

/**
 * Individual aspect.
 */
export default class Aspect extends React.Component {

    static propTypes = {
        id: React.PropTypes.number,
        title: React.PropTypes.string,
        onSubmit: React.PropTypes.func
    };

    constructor() {
        super();

        this.onSubmit = ::this.onSubmit;
    }

    /**
     * Man in the Middle. When a rating is given, an animation can play
     * before the item is removed from the list.
     */
    onSubmit() {
        this.props.onSubmit(this.props.id);
    }

    render() {
        return (
            <div className='item aspect'>
                <div className='header'>{this.props.title}</div>
                <RatingBar
                    onSubmit={this.onSubmit}
                />
            </div>
        );
    }

}
