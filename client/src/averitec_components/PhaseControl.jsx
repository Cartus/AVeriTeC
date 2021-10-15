import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import Button from '@material-ui/core/Button';

const EntryCard = styled(Card)`
  margin:10px;
`

const LogoutBox = styled.div`
    float:right;
    margin: 10px;
`

const PhaseHeader = styled.h4`
    margin: 10px 10px;
    float: left;
`

const PhaseDescriptionBox = styled.div`
    width: -webkit-calc(100% - 20px);
    width:    -moz-calc(100% - 20px);
    width:         calc(100% - 20px);
    float:left;
    padding:10px;
`

class PhaseControl extends React.Component {
    constructor(props) {
        super(props);

        this.onLogout = this.onLogout.bind(this)
    }

    onLogout(e){
        e.preventDefault();
        console.log('log out');
    };

    render() {
        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        return (
            <EntryCard className={className}>
                <PhaseHeader>{this.props.phaseName}</PhaseHeader>
                <LogoutBox>
                    <a href="#" onClick={this.onLogout}>Log out</a>
                </LogoutBox>
                <PhaseDescriptionBox>
                    {this.props.phaseInstructions}
                </PhaseDescriptionBox>
            </EntryCard>
        );
    }
}

export default PhaseControl;