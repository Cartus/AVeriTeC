import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import Button from '@material-ui/core/Button';

const EntryCard = styled(Card)`
  margin:10px;
`

const SubmitButton = styled(Button)`
float:left;
width:120px;
margin: 10px -webkit-calc(50% - 200px)!important;
margin:    10px -moz-calc(50% - 200px)!important;
margin:         10px calc(50% - 200px)!important;
`

const PrevButton = styled(Button)`
float:left;
width:120px;
margin:10px !important;
`

const NextButton = styled(Button)`
float:right;
width:120px;
margin:10px !important;
`

class NavBar extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <EntryCard>
                <PrevButton variant="contained" color="secondary" onClick={this.props.onPrevious}>
                  Previous
                </PrevButton>
                <SubmitButton variant="contained" color="primary" onClick={this.props.onSubmit}>
                  Submit
                </SubmitButton>
                <NextButton variant="contained" color="secondary" disabled={!this.props.hasSubmitted} onClick={this.props.onNext}>
                  Next
                </NextButton>
            </EntryCard>
        );
    }
}

export default NavBar;