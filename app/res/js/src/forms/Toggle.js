import React from 'react';
import ReactDOM from 'react-dom';

import { Input } from 'forms/Form';
import { Swipeable } from 'react-touch';

/**
 * Toggle input.
 */
export default class Toggle extends Input {
    constructor(props) {
        super(props);

        this.state = {
            checked: !!props.default
        };

        this.onClickLeft = ::this.onClickLeft;
        this.onClickRight = ::this.onClickRight;
    }

    onClickLeft() {
        this.setState({
            checked: !this.props.inverted
        });
    }

    onClickRight() {
        this.setState({
            checked: !!this.props.inverted
        });
    }

    render() {
        return (
            <Swipeable
                onSwipeLeft={this.onClickLeft}
                onSwipeRight={this.onClickRight}>
                <div className='toggle-input'>
                    <div className={
                        (this.state.checked ? 'positive' : 'negative') +
                        (this.props.inverted ? ' inverted' : '')
                    }>
                        <div className='slider'></div>
                        <div className='left' onClick={this.onClickLeft}>
                            {
                                this.props.inverted ?
                                (this.props.negativeLabel || 'No') :
                                (this.props.positiveLabel || 'Yes')
                            }
                        </div>
                        <div className='right' onClick={this.onClickRight}>
                            {
                                this.props.inverted ?
                                (this.props.positiveLabel || 'Yes') :
                                (this.props.negativeLabel || 'No')
                            }
                        </div>
                    </div>
                    <input
                        type='hidden'
                        value={this.state.checked.toString()}
                        name={this.props.name}
                    />
                </div>
            </Swipeable>
        );
    }
}
