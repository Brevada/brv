import React from 'react';
import ReactDOM from 'react-dom';

export default class Loader extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            message: ''
        };

        this.messages = props.messages || [];

        this.cycleMessage = this.cycleMessage.bind(this);
    }

    componentDidMount() {
        this.cycleMessage();
    }

    cycleMessage() {
        this.setState({
            message: this.messages[0]
        }, () => {
            this.messages.push(this.messages.shift());
            this.tmr = setTimeout(this.cycleMessage, Math.round(2000 + Math.random()*2000));
        });
    }

    componentWillUnmount() {
        clearTimeout(this.tmr);
    }

    render() {
        return (
            <div className={this.props.className}>
                <div className='loader'>
                    <i className='fa fa-spin fa-circle-o-notch'></i>
                    <div className='tip'>
                        <span>{this.state.message || 'Loading...'}</span>
                    </div>
                </div>
            </div>
        );
    }

}
