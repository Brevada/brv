import React from 'react';
import ReactDOM from 'react-dom';

import { Link } from 'components/Link';
import { Badges } from 'dashboard/aspects/Badges';
import EventAspects from 'dashboard/events/EventAspects';

import classNames from 'classnames';
import moment from 'moment';

export default class Event extends React.Component {
    constructor(props) {
        super(props);

        this.state = {};
    }

    render() {
        return (
            <div className='item constrain-w event'>
                <div className='ly constrain-w item dl event-content'>
                    <div className='dl header ly keep-spacing ly-split'>
                        <div className='left fill'>
                            <div className='title'>{this.props.title}</div>
                            <div className='hint'>
                                <span className='from'>{moment.unix(this.props.from).format('MMM. Do, YYYY')}</span>
                                <span className='delim'>{'\u2014'}</span>
                                <span className={classNames('to', {
                                    'completed': this.props.completed === false
                                })}>{
                                    this.props.completed === false ? 'Still Active' :
                                    moment.unix(this.props.completed).format('MMM. Do, YYYY')
                                }</span>
                            </div>
                        </div>
                        <div className='right'>
                            <Badges
                                industry={false}
                                filter={this.props.filter}
                                inline={true}
                                summary={this.props.summary}
                            />
                        </div>
                    </div>
                    <div className='body'>
                        <EventAspects
                            aspects={this.props.aspects}
                            onRemoveEvent={this.props.onRemove}
                            eventId={this.props.id}
                            storeId={this.props.storeId}
                        />
                    </div>
                </div>
            </div>
        );
    }

}
