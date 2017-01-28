import React from 'react';
import ReactDOM from 'react-dom';
import equal from 'deep-equal';

import { Link } from 'components/Link';
import { Badges } from 'dashboard/aspects/Badges';
import EventAspects from 'dashboard/events/EventAspects';
import Form, { Link as FormLink } from 'forms/Form';

import classNames from 'classnames';
import moment from 'moment';

export default class Event extends React.Component {
    constructor(props) {
        super(props);

        this.state = {};
    }

    shouldComponentUpdate(nextProps, nextState) {
        return !equal(this.props.aspects, nextProps.aspects) ||
               !equal(this.props.summary, nextProps.summary) ||
               !equal(this.props.title, nextProps.title) ||
               !equal(this.props.completed, nextProps.completed) ||
               !equal(this.props.from, nextProps.from) ||
               !equal(this.props.filter, nextProps.filter);
    }

    render() {
        return (
            <div className='item constrain-w event'>
                <div className='ly constrain-w item dl event-content'>
                    <div className='dl header ly keep-spacing ly-split'>
                        <div className='left fill'>
                            <div className='title' title={this.props.title}>{this.props.title}</div>
                            <div className='hint'
                                title={moment.unix(this.props.from).format('MMM. Do, YYYY') + ' - ' +
                                (this.props.completed === false ? 'Still Active' :
                                moment.unix(this.props.completed).format('MMM. Do, YYYY'))}
                            >
                                <span className='from'>
                                    {moment.unix(this.props.from).format('MMM. Do, YYYY')}
                                </span>
                                <span className='delim'>{'\u2014'}</span>
                                { (this.props.completed === false && (
                                    <Form
                                        action={`/api/event/${this.props.id}/complete`}
                                        method="POST"
                                        inline={true}
                                        onSuccess={this.props.onRefresh}
                                        onError={()=>false}>
                                        <FormLink
                                            className='to incomplete'
                                            submit={true}
                                            label={'Still Active'} />
                                    </Form>
                                )) || (
                                    <span className='to'>
                                        {moment.unix(this.props.completed).format('MMM. Do, YYYY')}
                                    </span>
                                ) }
                            </div>
                        </div>
                        <div className='right'>
                            <Badges
                                industry={false}
                                filter={this.props.filter}
                                inline={true}
                                average={this.props.summary.average}
                                to_all_time={this.props.summary.to_all_time}
                                responses={this.props.summary.responses}
                            />
                        </div>
                    </div>
                    <div className='body'>
                        <EventAspects
                            aspects={this.props.aspects}
                            onRemoveEvent={this.props.onRemove}
                            eventId={this.props.id}
                            storeId={this.props.storeId}
                            onRefresh={this.props.onRefresh}
                        />
                    </div>
                </div>
            </div>
        );
    }

}
