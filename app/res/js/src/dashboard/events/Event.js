import React from 'react';
import _ from 'lodash';

import classNames from 'classnames';
import { Badges } from 'dashboard/aspects/Badges';
import EventAspects from 'dashboard/events/EventAspects';
import Form from 'forms/Form';
import { Link } from 'forms/inputs/Button';
import { InlineRemove as InlineRemoveDialog } from 'dashboard/events/dialogs/InlineRemove';
import moment from 'moment';

/**
 * Event subtitle. Includes event active dates.
 *
 * @param {object} props
 */
const EventHint = props => (
    <div className='hint'
        title={moment.unix(props.from).format('MMM. Do, YYYY') + ' - ' +
        (props.completed === false ? 'Still Active' :
        moment.unix(props.completed).format('MMM. Do, YYYY'))}>
        <span className='from'>
            {moment.unix(props.from).format('MMM. Do, YYYY')}
        </span>
        <span className='delim'>{'\u2014'}</span>
        { (props.completed === false && (
            <Form
                action={`/api/event/${props.eventId}/complete`}
                method="POST"
                inline={true}
                onSuccess={props.onRefresh}
                onError={()=>false}>
                <Link
                    className='to incomplete'
                    submit={true}
                    label={'Still Active'} />
            </Form>
        )) || (
            <span className='to'>
                {moment.unix(props.completed).format('MMM. Do, YYYY')}
            </span>
        ) }
    </div>
);

/**
 * Event header.
 *
 * @param {object} props
 */
const EventHeader = props => (
    <div className='dl header ly keep-spacing ly-split'>
        <div className='left fill'>
            <div className='title' title={props.title}>{props.title}</div>
            <EventHint
                from={props.from}
                completed={props.completed}
                eventId={props.eventId}
                onRefresh={props.onRefresh}
            />
        </div>
        <div className='right'>
            <Badges
                industry={false}
                filter={props.filter}
                inline={true}
                average={props.summary.average}
                to_all_time={props.summary.to_all_time}
                responses={props.summary.responses}
            />
        </div>
    </div>
);

/**
 * Event body.
 *
 * @param {object} props
 */
const EventBody = props => (
    <div className='body'>
        <EventAspects
            aspects={props.aspects}
            onRemoveEvent={props.onRemove}
            eventId={props.eventId}
            storeId={props.storeId}
            onRefresh={props.onRefresh}
            onDelete={props.onDelete}
        />
    </div>
);

/**
 * Individual event.
 */
export default class Event extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            /* Indicates user has initiated delete process. */
            confirmingDelete: false,

            /* Indicates waiting on a response from the server regarding deletion. */
            deleting: false
        };

        /* User triggers deletion process for event. */
        this.onPromptConfirm = ::this.onPromptConfirm;

        this.onDeleteConfirmed = ::this.onDeleteConfirmed;
        this.onDeleteCanceled = ::this.onDeleteCanceled;
        this.onDeleteStarted = ::this.onDeleteStarted;
    }

    shouldComponentUpdate(nextProps, nextState) {
        /* Update if any part of the event has changed. */
        return !_.isEqual(this.state, nextState) ||
               !_.isEqual(this.props.aspects, nextProps.aspects) ||
               !_.isEqual(this.props.summary, nextProps.summary) ||
               !_.isEqual(this.props.title, nextProps.title) ||
               !_.isEqual(this.props.completed, nextProps.completed) ||
               !_.isEqual(this.props.from, nextProps.from) ||
               !_.isEqual(this.props.filter, nextProps.filter);
    }

    onPromptConfirm() {
        this.setState({
            confirmingDelete: true,
            deleting: false
        });
    }

    onDeleteConfirmed() {
        if (this.props.onRemove) {
            this.props.onRemove(this.props.id);
        }
    }

    onDeleteCanceled() {
        this.setState({
            confirmingDelete: false,
            deleting: false
        });
    }

    onDeleteStarted() {
        this.setState({
            deleting: true
        });
    }

    render() {
        /* Inline dialog shown if in remove mode. */
        const removeDialog = this.state.confirmingDelete && (
            <InlineRemoveDialog
                id={this.props.id}
                onCancel={this.onDeleteCanceled}
                onSuccess={this.onDeleteConfirmed}
                onError={this.onDeleteCanceled}
                onBegin={this.onDeleteStarted}
            />
        );

        return (
            <div className={classNames('item constrain-w event', {
                'state-removing': this.state.confirmingDelete && this.state.deleting
            })}>
                <div className='ly constrain-w item dl event-content'>
                    <EventHeader
                        title={this.props.title}
                        from={this.props.from}
                        completed={this.props.completed}
                        eventId={this.props.id}
                        onRefresh={this.props.onRefresh}
                        filter={this.props.filter}
                        summary={this.props.summary}
                    />
                    { removeDialog || (
                        <EventBody
                            aspects={this.props.aspects}
                            onRemove={this.props.onRemove}
                            eventId={this.props.id}
                            storeId={this.props.storeId}
                            onRefresh={this.props.onRefresh}
                            onDelete={this.onPromptConfirm}
                        />
                    ) }
                </div>
            </div>
        );
    }
}
