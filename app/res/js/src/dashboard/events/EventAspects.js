import React from 'react';
import ReactDOM from 'react-dom';
import equal from 'deep-equal';

import DataLayer from 'forms/DataLayer';
import Form, { Group as FormGroup, Link as FormLink } from 'forms/Form';
import AspectInputField from 'forms/AspectInputField';

import { Mood } from 'utils/Mood';
import classNames from 'classnames';

const EventAspectsItem = props => (
    <div className='event-aspects-item'>
        <div className='ly ly-split'>
            <div className='title left'>{props.title}</div>
            <div className='detail right fill'>
                { (props.responses > 0 && (
                    <span>
                        <span className={'change ' + Mood(props.change, -100)}>{
                            (props.change > 0 ? '+' : (props.change == 0 ? '' : '-')) + Math.abs(+props.change.toFixed(2)) + '%'
                        }</span>
                        <span className='text'>{` after ${props.responses} response${props.responses>1?'s':''}`}</span>
                    </span>
                )) || (
                    <span className='text'>{"no activity for this aspect"}</span>
                )}
            </div>
            <div className='control right'>
                <Form
                    action={`/api/event/${props.eventId}/aspect/${props.id}`}
                    method="DELETE"
                    onSuccess={()=>props.onRemove(props.id)}
                    onError={()=>false}
                >
                    <FormLink label='Remove' submit={true} danger={true} />
                </Form>
            </div>
        </div>
    </div>
);

const AspectInputFieldLinked = props => (
    <AspectInputField
        types={props.data.aspect_types || []}
        name='aspect'
        custom={false}
        submitOnSelect={true}
        placeHolder='+ Add New Aspect'
    />
);

export default class EventAspects extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            aspects: props.aspects || [],
            removed: [],
            refresh: 0
        };

        this.remove = this.remove.bind(this);
        this.refreshAspects = this.refreshAspects.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        if (!nextProps.aspects || equal(nextProps.aspects, this.state.aspects)) {
            return;
        }

        this.setState({
            aspects: nextProps.aspects
        });

        this.remove = this.remove.bind(this);
    }

    refreshAspects() {
        this.setState({
            refresh: this.state.refresh+1
        });
    }

    remove(id) {
        this.setState({
            removed: this.state.removed.concat([id])
        });
    }

    render() {
        return (
            <div>
                <div className='ly flex-v event-aspects'>
                    {this.state.aspects
                        .concat()
                        .filter(a => !this.state.removed.includes(a.id))
                        .sort((a,b) => a.title.localeCompare(b.title))
                        .map(aspect => (
                            <EventAspectsItem
                                key={aspect.id}
                                id={aspect.id}
                                eventId={this.props.eventId}
                                title={aspect.title}
                                change={aspect.change}
                                responses={aspect.responses}
                                onRemove={this.remove}
                            />
                    ))}
                </div>
                <div className='ly ly-split tools'>
                    <div className='left fill overflow'>
                        <Form
                            action={`/api/event/${this.props.eventId}/aspect`}
                            method="POST"
                            onSuccess={this.refreshAspects}
                            onError={()=>false}
                        >
                            <FormGroup className='new-aspect input-like small'>
                                <DataLayer
                                    action={`/api/aspecttypes/event/${this.props.eventId}`}
                                    refresh={this.state.refresh}>
                                    <AspectInputFieldLinked />
                                </DataLayer>
                            </FormGroup>
                        </Form>
                    </div>
                    <div className='right'>
                        <Form
                            action={`/api/event/${this.props.eventId}`}
                            method="DELETE"
                            onSuccess={()=>this.props.onRemoveEvent(this.props.eventId)}
                            onError={()=>false}
                        >
                            <FormLink label='Delete' submit={true} />
                        </Form>
                    </div>
                </div>

            </div>
        );
    }

}
