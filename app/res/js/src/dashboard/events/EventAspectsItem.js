import React, { Component } from "react";
import PropTypes from "prop-types";
import Form from "forms/Form";
import classNames from "classnames";
import { Link } from "forms/inputs/Button";
import { mood } from "utils/Mood";

/**
 * Event aspect deletion form.
 *
 * @param   {object} props React props
 * @param   {number} props.id The id of the event aspect to delete.
 * @param   {number} props.eventId The id of the event.
 * @param   {function} props.onRemove onRemove event handler.
 * @returns {JSX}
 */
const DeleteAspect = props => (
    <Form
        action={`/api/event/${props.eventId}/aspect/${props.id}`}
        method="DELETE"
        onSuccess={props.onRemove}
        onError={props.onError}
        onBegin={props.onBeginRemove}>
        <Link label="Remove" submit={true} danger={true} />
    </Form>
);

DeleteAspect.propTypes = {
    eventId: PropTypes.number.isRequired,
    id: PropTypes.number.isRequired,
    onRemove: PropTypes.func.isRequired,
    onError: PropTypes.func.isRequired,
    onBeginRemove: PropTypes.func.isRequired
};

/**
 * Details for an event aspect.
 *
 * @param   {object} props React props
 * @param   {string} props.className Custom classname to apply to details element.
 * @param   {number} props.responses The number of feedback responses.
 * @param   {number} props.change The percent change over the course of the event.
 * @returns {JSX}
 */
const AspectDetails = props => { // eslint-disable-line complexity
    const nChange = Math.round(+props.change);
    const sign = (nChange > 0 && "+") || (nChange < 0 && "-") || "";
    const change = sign + Math.abs(nChange) + "%";

    const noResponses = (
        <span className="text" title={"no activity for this aspect"}>
            {"no activity for this aspect"}
        </span>
    );

    const plural = props.responses > 1 ? "s" : "";

    /* Make positive values appear better than negative. */
    const moodChange = nChange > 0 ? mood(nChange, -50) : mood(nChange, -100);

    return (
        <div className={classNames("detail", props.className)}>
            {(props.responses > 0 && (
                (nChange === 0 && (
                    <span
                        title={`no change after ${props.responses} response${plural}`}>
                        <span className="text">
                            {`no change after ${props.responses} response${plural}`}
                        </span>
                    </span>
                )) || (
                    <span
                        title={change + ` after ${props.responses} response${plural}`}>
                        <span className={"change " + moodChange}>{change}</span>
                        <span className="text">
                            {` after ${props.responses} response${plural}`}
                        </span>
                    </span>
                )
            )) || noResponses}
        </div>
    );
};

AspectDetails.propTypes = {
    className: PropTypes.string,
    responses: PropTypes.number.isRequired,
    change: PropTypes.number.isRequired
};

AspectDetails.defaultProps = {
    className: ""
};

/**
 * Individual event aspect.
 *
 * @param   {object} props React props
 * @param   {string} props.title The name of the aspect.
 * @param   {number} props.responses The number of feedback responses.
 * @param   {number} props.change The percent change over the course of the event.
 * @param   {number} props.eventId The id of the event.
 * @param   {number} props.id The id of the event aspect to delete.
 * @param   {function} props.onRemove onRemove event handler.
 * @returns {void}
 */
class EventAspectsItem extends Component {

    static propTypes = {
        title: PropTypes.string.isRequired,
        responses: PropTypes.number.isRequired,
        change: PropTypes.number.isRequired,
        eventId: PropTypes.number.isRequired,
        id: PropTypes.number.isRequired,
        onRemove: PropTypes.func.isRequired
    };

    /**
     * @constructor
     */
    constructor() {
        super();

        this.state = {
            removing: false
        };

        this.onBeginRemove = ::this.onBeginRemove;
        this.onRemoveError = ::this.onRemoveError;
    }

    /**
     * Starts removal animation.
     * @returns {void}
     */
    onBeginRemove() {
        this.setState({ removing: true });
    }

    /**
     * Ends removal animation b/c remove failed.
     * @returns {void}
     */
    onRemoveError() {
        this.setState({ removing: false });
    }

    /**
     * @override
     */
    render () {
        return (
            <div
                className={classNames("event-aspects-item", {
                    "state-removing": this.state.removing
                })}>
                <div className="ly ly-abs-container">
                    <div
                        className="title left"
                        title={this.state.removing ? "" : this.props.title}>
                        {this.props.title}
                    </div>
                    <AspectDetails
                        className="left"
                        responses={this.props.responses}
                        change={this.props.change}
                    />
                    <div className="control right">
                        <DeleteAspect
                            eventId={this.props.eventId}
                            id={this.props.id}
                            onRemove={this.props.onRemove}
                            onBeginRemove={this.onBeginRemove}
                            onError={this.onRemoveError}
                        />
                    </div>
                </div>
            </div>
        );
    }
}

export { EventAspectsItem };
