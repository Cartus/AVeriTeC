import React from 'react';
import ClaimTopField from '../averitec_components/ClaimTopField';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';
import Button from '@material-ui/core/Button';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'
import NavBar from '../averitec_components/NavBar';

const EntryCard = styled(Card)`
  margin:10px;
`

const SubmitButton = styled(Button)`
float:right;
width:120px;
margin:10px !important;
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
                <ClaimTopField claim={this.state.claim} valid={this.state.valid} data={this.state.annotation} ask_for_justification onChange={this.handleFieldChange} id="annotation"/>
                {questionPairs}
                <NavBar onSubmit={this.doSubmit}/>
                <div>{JSON.stringify(this.state)}</div>
            </div>
        );
      }
}

export default VerdictValidation;