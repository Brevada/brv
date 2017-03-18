import React from 'react';

import { DropDownButton, DropDownOption } from 'components/DropDownNav';
import { NavigationButton } from 'components/NavigationButton';
import { Link } from 'components/Link';

const BrandBar = props => {
    return (
        <div className='brand-bar'>
            <div className='brand logo-lq logo'></div>
            <DropDownButton className='account' label={'Account'}>
                <DropDownOption
                    label={'Settings'}
                    onClick={()=>window.location.replace('/settings')}
                />
                <DropDownOption
                    label={'Logout'}
                    onClick={()=>window.location.replace('/logout')}
                />
            </DropDownButton>
            { props.stores && props.stores.length > 1 && (
                <DropDownButton className='stores' label={'Change Store'}>
                    {props.stores.map(store => (
                        <DropDownOption
                            label={store.name}
                            key={store.id}
                            active={store.id === props.storeId}
                            onClick={() => {
                                if (store.id !== props.storeId) {
                                    props.onStoreChange(store.id)
                                }
                            }}
                        />
                    ))}
                </DropDownButton>
            ) }
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
                <BrandBar
                    onStoreChange={this.props.onStoreChange}
                    stores={this.props.stores}
                    storeId={this.props.storeId}
                />
                <div className='view-navbar'>
                    <NavigationButton
                        label={'Your Aspects'}
                        value={'ASPECTS'}
                        onClick={this.props.onChangeView}
                        active={this.props.view === 'ASPECTS'}
                    />
                    <NavigationButton
                        label={'Events'}
                        value={'EVENTS'}
                        onClick={this.props.onChangeView}
                        active={this.props.view === 'EVENTS'}
                    />
                    {this.props.url && (
                        <Link
                            label={`brevada.com/${this.props.url}`}
                            onClick={()=>window.location.replace(`/${this.props.url}`)}
                        />
                    )}
                </div>
            </div>
        );
    }

}
