import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';

import DataLayer from 'forms/DataLayer';
import Form, { Group as FormGroup, Link as FormLink } from 'forms/Form';
import AspectInputField from 'forms/AspectInputField';

import { Mood } from 'utils/Mood';
import classNames from 'classnames';

const EventAspectsItem = props => (
    <div className='event-aspects-item'>
        <div className='ly ly-abs-container'>
            <div className='title left' title={props.title}>{props.title}</div>
            <div className='detail left'>
                { (props.responses > 0 && (
                    <span title={
                        (props.change > 0 ? '+' : (props.change == 0 ? '' : '-')) + Math.abs(+props.change.toFixed(2)) + '%'
                        + ` after ${props.responses} response${props.responses>1?'s':''}`
                    }>
                        <span className={'change ' + Mood(props.change, -100)}>{
                            (props.change > 0 ? '+' : (props.change == 0 ? '' : '-')) + Math.abs(+props.change.toFixed(2)) + '%'
                        }</span>
                        <span className='text'>
                            {` after ${props.responses} response${props.responses>1?'s':''}`}
                        </span>
                    </span>
                )) || (
                    <span className='text' title={"no activity for this aspect"}>
                        {"no activity for this aspect"}
                    </span>
                )}
            </div>
            <div className='control right'>
                <Form
                    action={`/api/event/${props.eventId}/aspect/${props.id}`}
                    method="DELETE"
                    onSuccess={props.onRemove}
                    onError={()=>false}
                >
                    <FormLink label='Remove' submit={true} danger={true} />
                </Form>
            </div>
        </div>
    </div>
);

const AspectInputFieldLinked = props => {
    return (
        <AspectInputField
            types={props.data.aspect_types || []}
            {...props}
        />
    );
};

export default class EventAspects extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            aspects: props.aspects || []
        };
    }

    componentWillReceiveProps(nextProps) {
        if (!nextProps.aspects || _.isEqual(nextProps.aspects, this.state.aspects)) {
            return;
        }

        this.setState({
            aspects: nextProps.aspects
        });
    }

    render() {
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
                        <Form
                            action={`/api/event/${this.props.eventId}/aspect`}
                            method="POST"
                            data={{store: this.props.storeId}}
                            onSuccess={this.props.onRefresh}
                            onError={()=>false}
                        >
                            <FormGroup className='new-aspect input-like small'>
                                <DataLayer
                                    action={`/api/aspecttypes/event/${this.props.eventId}`}>
                                    <AspectInputFieldLinked
                                        name='aspect'
                                        custom={false}
                                        submitOnSelect={true}
                                        placeHolder='+ Add New Aspect'
                                    />
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
