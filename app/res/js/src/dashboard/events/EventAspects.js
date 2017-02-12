import React from 'react';
import _ from 'lodash';

import DataLayer from 'forms/DataLayer';
import Form, { Group as FormGroup, Link as FormLink } from 'forms/Form';
import AspectInputField from 'forms/AspectInputField';
import { EventAspectsItem } from 'dashboard/events/EventAspectsItem';

/**
 * Links the aspect input field to the data layer.
 *
 * @param {object} props
 */
const AspectInputFieldLinked = props => {
    return (
        <AspectInputField
            types={props.data.aspect_types || []}
            {...props}
        />
    );
};

/**
 * Add aspect form used to add an aspect to an event.
 *
 * @param {object} props
 * @param {number} props.storeId
 * @param {number} props.eventId
 * @param {function} On aspect success callback.
 */
const AddAspect = props => (
    <Form
        action={`/api/event/${props.eventId}/aspect`}
        method="POST"
        data={{store: props.storeId}}
        onSuccess={props.onRefresh}
        onError={()=>false}
    >
        <FormGroup className='new-aspect input-like small'>
            <DataLayer
                action={`/api/aspecttypes/event/${props.eventId}`}>
                <AspectInputFieldLinked
                    name='aspect'
                    custom={false}
                    submitOnSelect={true}
                    placeHolder='+ Add New Aspect'
                />
            </DataLayer>
        </FormGroup>
    </Form>
);

/**
 * Event deletion form.
 *
 * @param {object} props
 * @param {number} props.eventId
 */
const DeleteEvent = props => (
    <Form
        action={`/api/event/${props.eventId}`}
        method="DELETE"
        onSuccess={()=>props.onRemoveEvent(props.eventId)}
        onError={()=>false}>
        <FormLink label='Delete' submit={true} />
    </Form>
);

/**
 * Collection of aspects to display for a particular event.
 */
export default class EventAspects extends React.Component {

    static propTypes = {
        aspects: React.PropTypes.arrayOf(React.PropTypes.object),
        eventId: React.PropTypes.number,
        storeId: React.PropTypes.number
    };

    constructor(props) {
        super(props);

        this.state = {
            /* Internal list of event aspects to display for a given event. */
            aspects: props.aspects || []
        };
    }

    componentWillReceiveProps(nextProps) {
        /* Only update event aspects list if aspects change. */

        if (!nextProps.aspects || _.isEqual(nextProps.aspects, this.state.aspects)) {
            return;
        }

        this.setState({
            aspects: nextProps.aspects
        });
    }

    render() {
        /* Sort event aspects by name. */
        return (
            <div>
                <div className='ly flex-v defined-size event-aspects'>
                    {this.state.aspects
                        .concat()
                        .sort((a,b) => a.title.localeCompare(b.title))
                        .map(aspect => (
                            <EventAspectsItem
                                key={aspect.id}
                                id={aspect.id}
                                eventId={this.props.eventId}
                                title={aspect.title}
                                change={aspect.change}
                                responses={aspect.responses}
                                onRemove={this.props.onRefresh}
                            />
                    ))}
                </div>
                <div className='ly ly-split tools'>
                    <div className='left fill overflow'>
                        <AddAspect
                            eventId={this.props.eventId}
                            storeId={this.props.storeId}
                            onRefresh={this.props.onRefresh}
                        />
                    </div>
                    <div className='right'>
                        <DeleteEvent
                            eventId={this.props.eventId}
                            onRemoveEvent={this.props.onRemoveEvent}
                        />
                    </div>
                </div>
            </div>
        );
    }

}
