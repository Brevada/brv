import React from 'react';
import ReactDOM from 'react-dom';

import { Link } from 'components/Link';

import { Badges } from 'dashboard/aspects/Badges';
import { Graph } from 'dashboard/aspects/Graph';
import { Filter } from 'dashboard/aspects/Filter';

import Form, { Group, Button, Group as FormGroup } from 'forms/Form';

import classNames from 'classnames';

const InlineRemoveDialog = props => (
    <div className='body'>
        <div className='remove-dialog'>
            <span>Are you sure you'd like to remove "{props.title}"?</span>
            <Form method="DELETE" action={`/api/aspect/${props.id}`} onSuccess={props.onSuccess} onError={()=>false}>
                <FormGroup className='link-style'>
                    <Button label="Remove" submit={true} right={true} />
                    <Button label="Cancel" left={true} onClick={props.onCancel} />
                </FormGroup>
            </Form>
        </div>
    </div>
);

export default class Aspect extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            visible: true,
            removing: false
        };

        this.toggleVisibility = this.toggleVisibility.bind(this);
        this.remove = this.remove.bind(this);
    }

    toggleVisibility() {
        /* Not currently used due to backend requirement. */
        this.setState({
            removing: false,
            visible: !this.state.visible
        });
    }

    remove() {
        this.setState({
            removing: true
        });
    }

    render() {
        return (
            <div className={classNames('item constrain-w aspect', { 'hidden': !this.state.visible })}>
                { !this.state.visible && (<div className={'hidden-badge'}><i className={'fa fa-eye-slash'}></i></div>) }
                <div className='ly contrain-w item dl aspect-content'>
                    <div className='dl header'>
                        <div className='hint'>{Filter.toLabel(this.props.filter)}</div>
                        <div className='title'>{this.props.title}</div>
                        { (!this.state.removing && (
                            <div className='links'>
                                <Link label={'Remove'} onClick={this.remove} />
                            </div>
                        )) || (
                            <div className='links filler'></div>
                        ) }
                    </div>
                    { this.state.removing && (
                        <InlineRemoveDialog
                            title={this.props.title}
                            id={this.props.id}
                            onCancel={()=>{this.setState({removing: false});}}
                            onSuccess={()=>{
                                if (this.props.onRemove){
                                    this.props.onRemove(this.props.id);
                                }
                            }}
                        />
                    ) || (
                        (this.props.summary.responses > 0 && (
                            <div className='body'>
                                <Badges summary={this.props.summary} filter={this.props.filter} />
                                <Graph data={this.props.summary.data.concat().sort((a, b) => a.to-b.to)} />
                            </div>
                        )) || (
                            <div className='body'>
                                <div className='blank-state'>
                                    <i className={'fa fa-sticky-note-o'}></i>
                                    <span>No Data Available</span>
                                </div>
                            </div>
                        )
                    ) }
                </div>
            </div>
        );
    }

}
