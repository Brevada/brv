import React from 'react';
import PropTypes from 'prop-types';
import _ from 'lodash';

import Fetch from 'forms/Fetch';
import Form, { Group } from 'forms/Form';
import { Link as FormLink } from 'forms/inputs/Button';
import AspectInputField from 'forms/inputs/AspectInputField';
import { EventAspectsItem } from 'dashboard/events/EventAspectsItem';

/**
 * Links the aspect input field to the data layer.
 *
 * @param {object} props
 */
const FetchedAspectInputField = props => {
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
        onError={()=>false}>
        <Group className='new-aspect input-like small'>
            <Fetch
                action={`/api/aspecttypes/event/${props.eventId}`}>
                <FetchedAspectInputField
                    name='aspect'
                    custom={false}
                    submitOnSelect={true}
                    placeHolder='+ Add New Aspect'
                />
            </Fetch>
        </Group>
    </Form>
);

/**
 * Collection of aspects to display for a particular event.
 */
export default class EventAspects extends React.Component {

    static propTypes = {
        aspects: PropTypes.arrayOf(PropTypes.object),
        eventId: PropTypes.number,
        storeId: PropTypes.number
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
                    <div className='right form'>
                        <FormLink
                            label='Delete'
                            onClick={this.props.onDelete}
                        />
                    </div>
                </div>
            </div>
        );
    }

}
