import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import {Redirect} from "react-router-dom";
import config from "../config.json"

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
        this.state = {
            login: true
        }

        this.onLogout = this.onLogout.bind(this)
    }

    onLogout(e){
        e.preventDefault();
        localStorage.clear();
        this.setState({login: false});
    };

    render() {
        if (!this.state.login) {
            return <Redirect to='/'/>;
        }

        localStorage.pc = 0;

        return (
            <EntryCard>
                <Header>Welcome, {this.props.user.username}!</Header>
                <LogoutBox>
                    <a target="_blank" rel="noopener noreferrer" href={config.api_url + "/guideline.pdf"} >Guidelines</a> | <a href="/change_password">Change Password</a> | <a href="#" onClick={this.onLogout}>Log out</a>
                </LogoutBox>
            </EntryCard>
        );
    }
}

export default UserPanel;