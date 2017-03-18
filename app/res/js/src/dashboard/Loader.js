import React from 'react';

/**
 * Loader animation which cycles through an array of messages.
 */
export default class Loader extends React.Component {

    static propTypes = {
        /* Messages to cycle through. */
        messages: React.PropTypes.arrayOf(React.PropTypes.string)
    }

    constructor(props) {
        super(props);

        this.state = {
            /* The current message to display. */
            message: ''
        };

        /* Internal collection of messages which are rotated through. */
        this.messages = props.messages || [];

        this.cycleMessage = ::this.cycleMessage;
    }

    componentDidMount() {
        /* Display the first message. */
        this.cycleMessage();
    }

    /**
     * Shift all messages over one.
     */
    cycleMessage() {
        this.setState({
            message: this.messages[0]
        }, () => {
            this.messages.push(this.messages.shift());
            /* Time between messages: random value in interval 2 - 4 seconds. */
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
                    <div className='spinner'>
                        <div className='bounce1'></div>
                        <div className='bounce2'></div>
                        <div className='bounce3'></div>
                    </div>
                    <div className='tip'>
                        <span>{this.state.message || 'Loading...'}</span>
                    </div>
                </div>
            </div>
        );
    }

}
