import React, { Component } from "react";
import PropTypes from "prop-types";

import { Filter } from "dashboard/aspects/Filter";

import AspectContainer from "dashboard/aspects/AspectContainer";
import TimeFilters from "dashboard/TimeFilters";

import { DialogButtonActions } from "dialogs/Dialog";
import NewAspectDialog from "dashboard/aspects/dialogs/NewAspect";
import Loader from "dashboard/Loader";

import Fetch from "forms/Fetch";

/**
 * Aspect container combined with a loading state.
 *
 * @param   {object} props React props
 * @param   {boolean} props.loading Whether the aspect container is loading data.
 * @param   {string} props.filter The time filter key from Filter.
 * @param   {object} props.data Data from the data layer.
 * @returns {JSX}
 */
const FetchedAspectContainer = props => {
    if (props.loading || !props.data) {
        return (<Loader messages={["Loading aspects..."]} />);
    }

    return (
        <AspectContainer
            filter={props.filter}
            aspects={props.data.aspects}
        />
    );
};

FetchedAspectContainer.propTypes = {
    loading: PropTypes.bool,
    data: PropTypes.object,
    filter: PropTypes.string.isRequired
};

FetchedAspectContainer.defaultProps = {
    data: null,
    loading: true
};

/**
 * Time filter toolbar for aspects view.
 *
 * @param   {object} props React props
 * @returns {JSX}
 */
const AspectTimeFilter = props => (
    <TimeFilters
        {...props}
        options={[
            { view: Filter.ensure("TODAY") },
            { view: Filter.ensure("PAST_WEEK") },
            { view: Filter.ensure("PAST_MONTH") },
            { view: Filter.ensure("ALL_TIME") }
        ]}
    />
);

/**
 * Main page view for Your Aspects.
 */
export default class AspectsView extends Component {

    static propTypes = {
        storeId: PropTypes.number.isRequired
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {
            /* Currently selected filter. */
            filter: Filter.ensure("ALL_TIME"),

            /* Visibility of dialogs. */
            dialogs: {
                "NEW_ASPECT": false
            },

            /* Simpler enforcement of data layer refresh than checking multiple
             * properties for a change. */
            refresh: 0
        };

        this.onChangeFilter = ::this.onChangeFilter;
        this.newAspectDialogAction = ::this.newAspectDialogAction;
        this.openDialog = ::this.openDialog;
    }

    /**
     * Event handler for filter change.
     * @param   {string} filter The filter key to change to.
     * @returns {void}
     */
    onChangeFilter(filter) {
        this.setState({ filter });
    }

    /**
     * Event handler for new aspect dialog action.
     * @param   {string} action The dialog action to perform.
     * @returns {void}
     */
    newAspectDialogAction(action) {
        if (![DialogButtonActions.CLOSE,
            DialogButtonActions.OPEN,
            DialogButtonActions.SUCCESS].includes(action)) {
            return;
        }

        this.setState(s => ({
            dialogs: Object.assign(s.dialogs, {
                "NEW_ASPECT": action === DialogButtonActions.OPEN
            })
        }), () => {
            if (action === DialogButtonActions.SUCCESS) {
                /* Force a data layer refresh. */
                this.setState(s => ({ refresh: s.refresh + 1 }));
            }
        });
    }

    /**
     * Opens new aspect dialog.
     * @returns {void}
     */
    openDialog() {
        this.newAspectDialogAction(DialogButtonActions.OPEN);
    }

    /**
     * @override
     */
    render() {
        return (
            <div className="view">
                {this.state.dialogs.NEW_ASPECT && (
                    <NewAspectDialog
                        storeId={this.props.storeId}
                        onAction={this.newAspectDialogAction}
                    />
                )}
                <AspectTimeFilter
                    onChange={this.onChangeFilter}
                    filter={this.state.filter}
                    actionLabel={"+ Ask Something New"}
                    onAction={this.openDialog}
                />
                <Fetch
                    action="/api/aspects"
                    data={{
                        store: this.props.storeId,
                        days: Filter.toDays(this.state.filter),
                        points: Filter.toPoints(this.state.filter)
                    }}
                    refresh={this.state.refresh}>
                    <FetchedAspectContainer
                        filter={this.state.filter}
                    />
                </Fetch>
            </div>
        );
    }
}
