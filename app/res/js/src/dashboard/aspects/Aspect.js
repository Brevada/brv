import React, { Component } from "react";
import PropTypes from "prop-types";
import classNames from "classnames";

import { HyperLink } from "components/HyperLink";
import { Badges } from "dashboard/aspects/Badges";
import { Graph } from "dashboard/aspects/Graph";
import { Filter } from "dashboard/aspects/Filter";
import { InlineRemove as InlineRemoveDialog } from "dashboard/aspects/dialogs/InlineRemove";

/**
 * Aspect header, with title and time interval indicator.
 * @param   {object} props React props
 * @param   {string} props.filter Time interval key (from Filter)
 * @param   {string} props.title The title of the aspect.
 * @param   {boolean} props.removing Indicates if in removing mode.
 * @param   {function} props.onRemove Callback invoked when "remove" is initiated.
 * @returns {JSX}
 */
const AspectHeader = props => (
    <div className="dl header">
        <div className="hint">{Filter.toLabel(props.filter)}</div>
        <div className="title">{props.title}</div>
        { (!props.removing && (
            <div className="links">
                <HyperLink label={"Remove"} onClick={props.onRemove} />
            </div>
        )) || (
            <div className="links filler"></div>
        ) }
    </div>
);

AspectHeader.propTypes = {
    filter: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    removing: PropTypes.bool,
    onRemove: PropTypes.func
};

AspectHeader.defaultProps = {
    removing: false,
    onRemove: () => { /* no op */ }
};

/**
 * Unused. Displays visibility indicator.
 *
 * @param   {object} props React props
 * @param   {boolean} props.visible Whether to show the hidden badge.
 * @returns {JSX}
 */
const HiddenBadge = props => props.visible && (
    <div className={"hidden-badge"}>
        <i className={"fa fa-eye-slash"}></i>
    </div>
);

HiddenBadge.propTypes = {
    visible: PropTypes.bool.isRequired
};

/**
 * Displayed if there's no data.
 * @returns {JSX}
 */
const BlankState = () => (
    <div className="body">
        <div className="blank-state">
            <i className={"fa fa-sticky-note-o"}></i>
            <span>{"No Data Available"}</span>
        </div>
    </div>
);

/**
 * Body of individual aspect, containing badges and graph.
 *
 * @param   {object} props React props
 * @param   {object} props.summary Summary of aspect stats.
 * @param   {string} props.filter Time interval filter from Filter.
 * @returns {JSX}
 */
const AspectBody = props => {
    if (props.summary.responses === 0) {
        return (<BlankState />);
    }

    if (props.nonStandard) {
        return (
            <div className="body">
                <div className="blank-state">
                    <i className={"fa fa-question-circle"}></i>
                    <span>{"Unsupported Aspect Type"}</span>
                </div>
            </div>
        );
    }

    return (
        <div className="body">
            <Badges
                average={props.summary.average}
                to_all_time={props.summary.to_all_time}
                responses={props.summary.responses}
                to_industry={props.summary.to_industry}
                filter={props.filter}
            />
            <Graph
                data={
                    props.summary.data.concat()
                                      .sort((a, b) => a.to - b.to)
                }
                filter={props.filter}
            />
        </div>
    );
};

AspectBody.propTypes = {
    summary: PropTypes.object.isRequired,
    filter: PropTypes.string.isRequired,
    nonStandard: PropTypes.bool
};

AspectBody.defaultProps = {
    nonStandard: false
};

/**
 * An individual aspect, complete with various aspect related statistics.
 */
export default class Aspect extends Component {

    static propTypes = {
        title: PropTypes.string.isRequired,
        id: PropTypes.number.isRequired,
        summary: PropTypes.object.isRequired,
        filter: PropTypes.string.isRequired,
        onRemove: PropTypes.func,
        nonStandard: PropTypes.bool
    };

    static defaultProps = {
        onRemove: () => { /* no op */ },
        nonStandard: false
    };

    /**
     * @constructor
     * @param   {object} props React props
     */
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
        this.onRemoveCancel = ::this.onRemoveCancel;
        this.onRemoveSuccess = ::this.onRemoveSuccess;
    }

    /**
     * Toggles "hidden" status of aspect.
     *
     * @TODO Unimplemented due to lack of backend functionality.
     * @returns {void}
     */
    toggleVisibility() {
        this.setState(s => ({
            removing: false,
            visible: !s.visible
        }));
    }

    /**
     * Enables the remove mode, thus showing the remove dialog.
     * @returns {void}
     */
    remove() {
        this.setState({
            removing: true
        });
    }

    /**
     * Handles on remove cancel.
     * @returns {void}
     */
    onRemoveCancel() {
        this.setState({ removing: false });
    }

    /**
     * Handles on remove success.
     * @returns {void}
     */
    onRemoveSuccess() {
        if (this.props.onRemove) this.props.onRemove(this.props.id);
    }

    /**
     * @override
     */
    render() {
        /* Inline dialog shown if in remove mode. */
        const removeDialog = this.state.removing && (
            <InlineRemoveDialog
                title={this.props.title}
                id={this.props.id}
                onCancel={this.onRemoveCancel}
                onSuccess={this.onRemoveSuccess}
            />
        );

        return (
            <div
                className={classNames("item constrain-w aspect", {
                    "hidden": !this.state.visible
                })}>
                <HiddenBadge
                    visible={!this.state.visible}
                />
                <div className="ly contrain-w item dl aspect-content">
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
                            nonStandard={this.props.nonStandard}
                        />
                    ) }
                </div>
            </div>
        );
    }
}
