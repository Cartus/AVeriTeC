import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';

const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px;
`

const Header = styled.h4`
`

class UserPanel extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <EntryCard>
                <Header>Welcome, {this.props.user.username}!</Header>
            </EntryCard>
        );
    }
}

export default UserPanel;