import React from 'react';
import classNames from 'classnames';

import { Link } from 'components/Link';
import { Badges } from 'dashboard/aspects/Badges';
import { Graph } from 'dashboard/aspects/Graph';
import { Filter } from 'dashboard/aspects/Filter';
import { InlineRemove as InlineRemoveDialog } from 'dashboard/aspects/dialogs/InlineRemove';

/**
 * Aspect header, with title and time interval indicator.
 * @param {object} props
 * @param {string} props.filter Time interval key (from Filter)
 * @param {string} props.title The title of the aspect.
 *
 * @param {boolean} props.removing Indicates if in removing mode.
 * @param {function} props.onRemove Callback invoked when "remove" is initiated.
 */
const AspectHeader = props => (
    <div className='dl header'>
        <div className='hint'>{Filter.toLabel(props.filter)}</div>
        <div className='title'>{props.title}</div>
        { (!props.removing && (
            <div className='links'>
                <Link label={'Remove'} onClick={props.onRemove} />
            </div>
        )) || (
            <div className='links filler'></div>
        ) }
    </div>
);

/**
 * Unused. Displays visibility indicator.
 *
 * @param {object} props
 * @param {boolean} props.visible Whether to show the hidden badge.
 */
const HiddenBadge = props => props.visible && (
    <div className={'hidden-badge'}>
        <i className={'fa fa-eye-slash'}></i>
    </div>
);

/**
 * Displayed if there's no data.
 *
 * @param {object} props
 */
const BlankState = props => (
    <div className='body'>
        <div className='blank-state'>
            <i className={'fa fa-sticky-note-o'}></i>
            <span>No Data Available</span>
        </div>
    </div>
);

/**
 * Body of individual aspect, containing badges and graph.
 *
 * @param {object} props
 * @param {object} props.summary
 * @param {string} props.filter Time interval filter from Filter.
 */
const AspectBody = props => (
    props.summary.responses === 0 ?
    <BlankState /> :
    (<div className='body'>
        <Badges
            average={props.summary.average}
            to_all_time={props.summary.to_all_time}
            responses={props.summary.responses}
            to_industry={props.summary.to_industry}
            filter={props.filter}
        />
        <Graph data={props.summary.data.concat().sort((a, b) => a.to-b.to)} />
    </div>)
);

/**
 * An individual aspect, complete with various aspect related statistics.
 */
export default class Aspect extends React.Component {

    static propTypes = {
        title: React.PropTypes.string.isRequired,
        id: React.PropTypes.number.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            /* Not used. Represents "hidden" status of aspect. */
            visible: true,

            /* If true, will display remove dialog. */
            removing: false
        };

        this.toggleVisibility = ::this.toggleVisibility;
        this.remove = ::this.remove;
    }

    /**
     * Toggles "hidden" status of aspect.
     *
     * @TODO Unimplemented due to lack of backend functionality.
     */
    toggleVisibility() {
        this.setState(s => ({
            removing: false,
            visible: !s.visible
        }));
    }

    /**
     * Enables the remove mode, thus showing the remove dialog.
     */
    remove() {
        this.setState({
            removing: true
        });
    }

    render() {
        /* Inline dialog shown if in remove mode. */
        const removeDialog = this.state.removing && (
            <InlineRemoveDialog
                title={this.props.title}
                id={this.props.id}
                onCancel={()=>this.setState({removing: false})}
                onSuccess={()=>this.props.onRemove && this.props.onRemove(this.props.id)}
            />
        );

        return (
            <div className={classNames('item constrain-w aspect', {
                'hidden': !this.state.visible
            })}>
                <HiddenBadge
                    visible={!this.state.visible}
                />
                <div className='ly contrain-w item dl aspect-content'>
                    <AspectHeader
                        filter={this.props.filter}
                        title={this.props.title}
                        removing={this.state.removing}
                        onRemove={this.remove}
                    />
                    { removeDialog || (
                        <AspectBody
                            filter={this.props.filter}
                            summary={this.props.summary}
                        />
                    ) }
                </div>
            </div>
        );
    }
}
