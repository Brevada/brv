import React from 'react';

import { DropDownButton, DropDownOption } from 'components/DropDownNav';
import { NavigationButton } from 'components/NavigationButton';
import { Link } from 'components/Link';

const BrandBar = props => {
    return (
        <div className='brand-bar'>
            <div className='logo'></div>
            <DropDownButton label={'Account'}>
                <DropDownOption
                    label={'Settings'}
                    onClick={()=>window.location.replace('/settings')}
                />
                <DropDownOption
                    label={'Logout'}
                    onClick={()=>window.location.replace('/logout')}
                />
            </DropDownButton>
        </div>
    );
};

/**
 * Main dashboard navigation bar. Also contains top brand bar.
 */
export default class NavigationBar extends React.Component {
    constructor() {
        super();
    }

    render() {
        return (
            <div className='navigation-bar'>
                <BrandBar />
                <div className='view-navbar'>
                    <NavigationButton
                        label={'Timeline'}
                        view={'TIMELINE'}
                        onClick={this.props.onChangeView}
                        active={this.props.view === 'TIMELINE'}
                    />
                    <NavigationButton
                        label={'Your Aspects'}
                        view={'ASPECTS'}
                        onClick={this.props.onChangeView}
                        active={this.props.view === 'ASPECTS'}
                    />
                    <NavigationButton
                        label={'Events'}
                        view={'EVENTS'}
                        onClick={this.props.onChangeView}
                        active={this.props.view === 'EVENTS'}
                    />
                    {this.props.url && (
                        <Link
                            label={`Feedback Page: brevada.com/${this.props.url}`}
                            onClick={()=>window.location.replace(`/${this.props.url}`)}
                        />
                    )}
                </div>
            </div>
        );
    }

}
