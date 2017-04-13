import React from 'react';
import classNames from 'classnames';
import Form from 'forms/Form';
import stateQueue from 'utils/StateQueue';

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
 * A message to display when a user has given feedback for an aspect.
 * @param {object} props
 */
const Submitted = props => (
    <div className='submitted'>
        Thank you for giving feedback.
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

        this.state = {
            /* Indicates that feedback for this aspect has already been
             * submitted. */
            submitted: false,

            /* Indicates that feedback is being submitted. */
            submitting: false,

            /* Indicates that the aspect is in the process of being removed. */
            removing: false,

            /* Rating data. */
            value: '',
            ordinal: ''
        };

        this.form = null;

        this.onSubmit = ::this.onSubmit;
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Man in the Middle. When a rating is given, an animation can play
     * before the item is removed from the list.
     */
    onSubmit(value, ordinal) {
        if (this.state.submitted || this.state.submitting) return;

        this.setState({
            value: parseFloat(value),
            ordinal: parseInt(ordinal)
        }, () => {
            /* Submit data. */
            this.form && this.form.submit();

            /* Play out animation. */
            stateQueue(this, () => !this._unmounted)
                .do({ submitting: true })
                .wait(250)
                .do({
                    submitted: true,
                    submitting: false
                })
                .wait(700)
                .do({
                    removing: true
                })
                .wait(300)
                .do(() => {
                    this.props.onSubmit(this.props.id);
                })
                .exec();
        });
    }

    render() {
        return (
            <Form
                method="POST"
                action="/api/feedback/response"
                data={{
                    store: brv.feedback.id() || false,
                    session: this.props.session,
                    aspect_id: this.props.id,
                    value: this.state.value,
                    ordinal: this.state.ordinal
                }}
                form={f => this.form = f}>
                <div
                    className={classNames('item', 'aspect', {
                        submitting: this.state.submitting && !this.state.submitted,
                        removing: this.state.removing
                    })}>
                    <div className='header'>{this.props.title}</div>
                    { (this.state.submitted && (
                        <Submitted />
                    )) || (
                        <RatingBar
                            onSubmit={this.onSubmit}
                        />
                    ) }
                </div>
            </Form>
        );
    }

}
