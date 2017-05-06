import React from 'react';

import Fetch from 'forms/Fetch';
import Dialog, { DialogButton, DialogButtonActions } from 'dialogs/Dialog';
import Form, { Group, Label, ErrorMessage } from 'forms/Form';
import { Button } from 'forms/inputs/Button';
import AspectInputField from 'forms/inputs/AspectInputField';

/**
 * Connects Aspect Input Field with Data Layer.
 *
 * @param {object} props
 * @param {object} props.data Data passed from data layer.
 */
const FetchedAspectInputField = props => (
    <AspectInputField
        types={props.data.aspect_types || []}
        name='aspect'
        custom={true}
        placeHolder="type something here"
    />
);

/**
 * New aspect dialog.
 */
export default class NewAspect extends React.Component {
    constructor() {
        super();

        this.state = {
            createError: null
        };

        this.submitError = ::this.submitError;
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    /**
     * Event handler for form submission error.
     */
    submitError(error) {
        if (this._unmounted) return;
        this.setState({
            createError: error.data.reason ||
            `Unknown error: ${error.status}`
        });
    }

    render() {
        return (
            <Dialog
                defaultShow={this.props.show}
                onAction={this.props.onAction}
                escapable={true}>
                <Form
                    method="POST"
                    action="/api/aspect"
                    data={{ store: this.props.storeId }}
                    onSuccess={()=>this.props.onAction(DialogButtonActions.SUCCESS)}
                    onError={this.submitError}>
                    { this.state.createError !== null && (
                        <ErrorMessage text={this.state.createError} />
                    ) }

                    <Group className='new-aspect'>
                        <Label
                            text={'How satisfied are customers with'}
                            inline={true}
                        />
                        <Fetch
                            action="/api/aspecttypes/industry"
                            data={{exclude_store: this.props.storeId}}>
                            <FetchedAspectInputField />
                        </Fetch>
                        <Label
                            text={'?'}
                            inline={true}
                        />
                    </Group>
                    <Group className='dialog-controls link-style'>
                        <Button
                            label='Add Question'
                            submit={true}
                            right={true}
                        />
                        <Button
                            label='Cancel'
                            onClick={()=>this.props.onAction(DialogButtonActions.CLOSE)}
                            right={true}
                        />
                    </Group>
                </Form>
            </Dialog>
        );
    }
}
