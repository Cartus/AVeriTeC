import React from 'react';
import ValidationClaimTopField from '../averitec_components/ValidationClaimTopField';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';
import Button from '@material-ui/core/Button';
import { notEmptyValidator, atLeastOneValidator } from '../utils/validation.js'
import NavBar from '../averitec_components/NavBar';
import PhaseControl from '../averitec_components/PhaseControl';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import axios from "axios";
import { Redirect } from "react-router-dom";
import config from "../config.json";

const EntryCard = styled(Card)`
  margin:10px;
`

const RightPhaseControl = styled(PhaseControl)`
@media (max-width: 1300px)  {
  margin: 10px 10px 0px 10px;
}

@media (min-width: 1299px)  {
  height:260px;
  margin: 10px 10px 10px 0px;
}
`

const RightBox = styled.div`
  @media (max-width: 1300px)  {
    width:100%;
  }

  @media (min-width: 1299px)  {
    width:30%;
  }

  float: right;
`

const LeftBox = styled.div`
  @media (max-width: 1300px)  {
    width:100%;
  }

  @media (min-width: 1299px)  {
    width:70%;
  }

  float: left;
`

const QABox = styled.div`
  width: 100%;
  float: left;
  margin: -10px 0px 0px 0px;
`

class VerdictValidationBar extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let claim = this.props.claim
        if (!claim) {
            console.log("warning: null claim")
            claim = {}
        }

        let annotation = this.props.annotation
        if (!annotation) {
            console.log("warning: null annotation")
            annotation = {}
        }

        var questionPairs = ""

        if (claim && claim.questions) {
            questionPairs = Object.keys(claim.questions).map(question_id => (
                <EntryCard variant="outlined">
                    <StaticQuestionEntryField posthocView={this.props.posthocView} id={question_id} data={claim.questions[question_id]} onChange={this.props.handleFieldChange} />
                </EntryCard>
            ));
        }

        return <div>
            {!this.props.posthocView ?
                <div>
                    <RightBox>
                        <RightPhaseControl current_idx={this.props.current_idx} final_idx={this.props.final_idx} phaseName="Quality Control" phaseInstructions="Please read the claim and the question-answer pairs. Then, give your verdict on the claim. Do not look at any external information; make your verdict based ONLY on the question-answer pairs. If there are any problems with a question-answer pair, please use the form to report it. Do not use the information in any question-answer pair you report to make your verdict." />
                    </RightBox>
                    <LeftBox>
                        <ValidationClaimTopField shouldUseVagueLabel={this.props.shouldUseVagueLabel} posthocView={this.props.posthocView} claim={claim} valid={this.props.valid} data={annotation} ask_for_justification onChange={this.props.handleFieldChange} id="annotation" />
                    </LeftBox>
                </div>

                : <ValidationClaimTopField shouldUseVagueLabel={this.props.shouldUseVagueLabel} posthocView={this.props.posthocView} claim={claim} valid={this.props.valid} data={annotation} ask_for_justification onChange={this.props.handleFieldChange} id="annotation" />
            }
            <QABox >
                <div data-tour="question_view">
                    {questionPairs}
                </div>
                {!this.props.posthocView ?
                    <NavBar onPrevious={this.props.doPrevious} onSubmit={this.props.doSubmit} onNext={this.props.doNext} dataset={this.props.dataset} />
                    : ""
                }
            </QABox>
        </div>
    }
}


export default VerdictValidationBar;