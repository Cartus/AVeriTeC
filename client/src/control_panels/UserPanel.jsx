import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';

const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px;
`

const Header = styled.h4`
    float:left;
`

const LogoutBox = styled.div`
    float:right;
    margin: 20px 0px 0px 0px;
`


class UserPanel extends React.Component {
    constructor(props) {
        super(props);
    }


    onLogout(e){
        e.preventDefault();
        console.log('log out');
    };

    render() {
        return (
            <EntryCard>
                <Header>Welcome, {this.props.user.username}!</Header>
                <LogoutBox>
                    <a href="#" onClick={this.onLogout}>Log out</a>
                </LogoutBox>
            </EntryCard>
        );
    }
}

export default UserPanel;