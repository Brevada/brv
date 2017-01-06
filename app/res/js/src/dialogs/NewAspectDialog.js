import React from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import axios from 'axios';

import Dialog, { DialogButton, DialogButtonActions } from './Dialog';
import Form, { Group as FormGroup, Label as FormLabel, Button, ErrorMessage } from '../forms/Form';

import { Link } from '../components/Link';
import AspectInputField from '../forms/AspectInputField';

export default class NewAspectDialog extends React.Component {
    constructor() {
        super();

        this.state = {
            types: [],

            loading: false,
            error: null,

            createError: null
        };

        this.fetchTypes = this.fetchTypes.bind(this);
        this.submitError = this.submitError.bind(this);
    }

    componentWillReceiveProps(next) {
        if (!this.props.storeId && !!next.storeId) {
            this.fetchTypes();
        }
    }

    componentDidMount() {
        this.fetchTypes();
    }

    componentWillUnmount() {
        this._unmounted = true;
    }

    fetchTypes() {
        if (!this.props.storeId || this.state.loading) return;

        this.setState({
            loading: true,
            error: null
        }, () => {
            axios.get('/api/aspecttypes/industry', {
                params: {
                    'exclude_store': this.props.storeId
                }
            })
            .then(res => {
                if (this._unmounted) return;
                this.setState({
                    loading: false,
                    types: res.data.aspect_types || [],
                    error: null
                });
            })
            .catch(err => {
                if (this._unmounted) return;
                this.setState({
                    loading: false,
                    error: err.reason || `An unknown error has occured: ${err.code}`
                });
            });
        });
    }

    submitError(error) {
        if (this._unmounted) return;
        this.setState({ createError: error.data.reason || `Unknown error: ${error.status}` });
    }

    render() {
        return (
            <Dialog defaultShow={this.props.show} onAction={this.props.onAction}>
                <Form
                    method="POST"
                    action="/api/aspect"
                    data={{ store: this.props.storeId }}
                    onSuccess={()=>this.props.onAction(DialogButtonActions.SUCCESS)}
                    onError={this.submitError}
                >
                    { this.state.createError !== null && (
                        <ErrorMessage text={this.state.createError} />
                    ) }

                    <FormGroup className='new-aspect'>
                        <FormLabel
                            text={'How satisfied are customers with'}
                            inline={true}
                        />
                        <AspectInputField
                            types={this.state.types}
                            loading={this.state.loading}
                            name={'aspect'}
                            placeHolder={"type something here"}
                        />
                        <FormLabel
                            text={'?'}
                            inline={true}
                        />
                    </FormGroup>
                    <FormGroup className='dialog-controls link-style'>
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
                    </FormGroup>
                </Form>
            </Dialog>
        );
    }
}
