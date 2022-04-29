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
        let phase = localStorage.getItem('phase');

        let finished_num = 0;
        if (phase === 'phase_1') {
            finished_num = Number(localStorage.finished_norm_annotations);
        } else if (phase === 'phase_2') {
            finished_num = Number(localStorage.finished_qa_annotations);
        } else if (phase === 'phase_3') {
            finished_num = Number(localStorage.finished_valid_annotations);
        } else if (phase === 'phase_4') {
          finished_num = Number(localStorage.finished_p4_annotations);
        } else if (phase === 'phase_5') {
            finished_num = Number(localStorage.finished_p5_annotations);
        }

        return (
            <EntryCard>
                <PrevButton variant="contained" color="secondary" disabled={Number(localStorage.pc) === finished_num} onClick={this.props.onPrevious}>
                  Previous
                </PrevButton>
                <SubmitButton data-tour="submit" variant="contained" color="primary" onClick={this.props.onSubmit}>
                  Submit
                </SubmitButton>
                <NextButton variant="contained" color="secondary" disabled={Number(localStorage.pc) <= 0}  onClick={this.props.onNext}>
                  Next
                </NextButton>
            </EntryCard>
        );
    }
}

export default NavBar;