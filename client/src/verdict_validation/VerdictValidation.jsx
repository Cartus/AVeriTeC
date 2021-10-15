import React from 'react';
import ValidationClaimTopField from '../averitec_components/ValidationClaimTopField';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';
import Button from '@material-ui/core/Button';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'
import NavBar from '../averitec_components/NavBar';
import PhaseControl from '../averitec_components/PhaseControl';

const EntryCard = styled(Card)`
  margin:10px;
`

const RightPhaseControl = styled(PhaseControl)`
@media (max-width: 1290px)  {
  margin: 10px 10px 0px 10px;
}

@media (min-width: 1291px)  {
  height:230px;
  margin: 10px 10px 10px 0px;
}
`

const RightBox = styled.div`
  @media (max-width: 1290px)  {
    width:100%;
  }

  @media (min-width: 1291px)  {
    width:30%;
  }

  float: right;
`

const LeftBox = styled.div`
  @media (max-width: 1290px)  {
    width:100%;
  }

  @media (min-width: 1291px)  {
    width:70%;
  }

  float: left;
`

const QABox = styled.div`
  width: 100%;
  float: left;
  margin: -10px 0px 0px 0px;
`

function validate(content){
    var valid = true

    if(!("label" in content["annotation"]) || notEmptyValidator(content["annotation"]["label"]).error){
        valid = false;
    } else if(!("justification" in content["annotation"]) || notEmptyValidator(content["annotation"]["justification"]).error){
        valid = false;
    }
  
    return valid;
  }

class VerdictValidation extends React.Component {
    constructor(props) {
        super(props);
        
        this.state = {
            claim : {
                web_archive: "https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/",
                claim_text: "New England Journal of Medicine finds that women who got v4x3d – within 30 days of becoming pregnant and up to 20 weeks pregnant – had a miscarriage rate of 82%",
                claim_speaker: "Ian Smith",
                claim_type: ["Numerical Claim"],
                fact_checking_strategy: ["Numerical Comparison", "Consultation"],
                claim_hyperlink: "https://archive.is/qpiqn",
                claim_date: "15/06/2021",
                questions: {
                    "question_1":
                    {
                        text: "is this a question?",
                        answer_type: "Abstractive",
                        answer: "this is definitely 100% a legit question.",
                        url: "www.abc.def/ghi"
                    },
                    "question_2": {
                        text: "is this also question?",
                        answer_type: "Abstractive",
                        answer: "this is definitely 100% not a legit question.",
                        url: "www.abc.def/asdfasdf"
                    }
                }
            },
            annotation: {},
            valid: true
        }

        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.doSubmit = this.doSubmit.bind(this)
      }

      handleFieldChange(fieldId, element, value) {
        if (fieldId === "annotation"){
          this.setState(prevState => ({
            [fieldId]: {
                  ...prevState[fieldId],
                  [element]: value
              }
          }))  
        } else{
          this.setState(prevState => ({
              claim:{
                ...prevState.claim,
                questions: {
                    ...prevState.claim.questions,
                    [fieldId]: {
                        ...prevState.claim.questions[fieldId],
                        [element]: value
                    }
                }
            }
          }))
        }   
        }

        doSubmit(){
            if (validate(this.state)){
              window.alert(validate(this.state));
            } else{
              this.setState({
                valid: false
              });
            }
      
            // If valid, submit
      
            // If not, turn on error display
          }

    render() {
        const questionPairs = Object.keys(this.state.claim.questions).map(question_id => (
            <EntryCard variant="outlined">
                <StaticQuestionEntryField id={question_id} question={this.state.claim.questions[question_id]} onChange={this.handleFieldChange}/>
            </EntryCard>
          ));

        return (
            <div>
              <RightBox>
                <RightPhaseControl phaseName="Verdict Validation" phaseInstructions="Please read the claim and the question-answer pairs. Then, give your verdict on the claim. Do not look at any external information; make your verdict based ONLY on the question-answer pairs. If there are any problems with a question-answer pair, please use the form to report it. Do not use the information in any question-answer pair you report to make your verdict."/>
              </RightBox>
                <LeftBox>
                  <ValidationClaimTopField claim={this.state.claim} valid={this.state.valid} data={this.state.annotation} ask_for_justification onChange={this.handleFieldChange} id="annotation"/>
                </LeftBox>
                <QABox>
                  {questionPairs}
                  <NavBar onSubmit={this.doSubmit}/>
                </QABox>
                <div>{JSON.stringify(this.state)}</div>
            </div>
        );
      }
}

export default VerdictValidation;