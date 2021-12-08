import React from 'react';
import ValidationClaimTopField from '../averitec_components/ValidationClaimTopField';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';
import Button from '@material-ui/core/Button';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'
import NavBar from '../averitec_components/NavBar';
import PhaseControl from '../averitec_components/PhaseControl';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import axios from "axios";
import {Redirect} from "react-router-dom";
import config from "../config.json";

const EntryCard = styled(Card)`
  margin:10px;
`

const RightPhaseControl = styled(PhaseControl)`
@media (max-width: 1290px)  {
  margin: 10px 10px 0px 10px;
}

@media (min-width: 1291px)  {
  height:260px;
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
        valid = content["annotation"]["unreadable"];
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
                web_archive: "",
                claim_text: "",
                claim_speaker: "",
                claim_hyperlink: "",
                claim_date: "",
                questions: {}
            },
            annotation: {},
            valid: true,
            submitted: false,
            userIsFirstVisiting: false
        }

        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.doSubmit = this.doSubmit.bind(this);
        this.doPrevious = this.doPrevious.bind(this);
        this.doNext = this.doNext.bind(this);
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

    componentDidMount() {
        if (localStorage.getItem('login')) {
            let pc = Number(localStorage.pc);
            console.log(pc);
            if (pc !== 0){
		        var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/verdict_validate.php",
                    data:{
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'reload-data',
                        offset: pc - 1
                    }
                };

                axios(request).then((response) => {
                    if (response.data) {
                        console.log(response.data);
                        const new_claim = {
                            web_archive: response.data.web_archive,
                            claim_text: response.data.claim_text,
                            claim_speaker: response.data.speaker,
                            claim_hyperlink: response.data.claim_hyperlink,
                            claim_date: response.data.claim_date,
                            questions: response.data.questions
                        };
                        localStorage.claim_norm_id = response.data.claim_norm_id;
                        this.setState({claim: new_claim});
                        this.setState({annotation: response.data.annotation});
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {window.alert(error)})    
            } else {
                var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/verdict_validate.php",
                    data:{
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'next-data'
                    }
                };

                axios(request).then((response) => {
                    if (response.data) {
                        console.log(response.data);
                        if (Number(localStorage.finished_valid_annotations) === 0) {
                            this.setState({userIsFirstVisiting: true});
                        }
                        const new_claim = {
                            web_archive: response.data.web_archive,
                            claim_text: response.data.claim_text,
                            claim_speaker: response.data.speaker,
                            claim_hyperlink: response.data.claim_hyperlink,
                            claim_date: response.data.claim_date,
                            questions: response.data.questions
                        };
                        this.setState({claim: new_claim});
                        console.log(this.state.claim);
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {window.alert(error)})    
            }
        }
    }

    async doSubmit() {
        if (validate(this.state)){
            let pc = Number(localStorage.pc);
            if (pc !== 0) {
                localStorage.pc = Number(localStorage.pc) - 1;
		        var request = {
                    method: "post",
                    baseURL:  config.api_url,
                    url: "/verdict_validate.php",
                    data:{
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'resubmit-data',
                        annotation: this.state.annotation,
                        questions: this.state.claim.questions,
                        claim_norm_id: localStorage.claim_norm_id
                    }
                };

                await axios(request).then((response) => {
                    console.log(response.data);
                    localStorage.claim_norm_id = 0;
                    window.location.reload(false);
                }).catch((error) => {window.alert(error)})
            } else {
		        var request = {
                    method: "post",
                    baseURL:  config.api_url,
                    url: "/verdict_validate.php",
                    data:{
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'submit-data',
                        annotation: this.state.annotation,
                        questions: this.state.claim.questions
                    }
                };

                await axios(request).then((response) => {
                    console.log(response.data);
                    localStorage.finished_valid_annotations = Number(localStorage.finished_valid_annotations) + 1;
                    window.location.reload(false);
                }).catch((error) => {window.alert(error)})    
            }
        } else{
            this.setState({
                valid: false
            });
        }
    }

    doPrevious() {
        localStorage.pc = Number(localStorage.pc) + 1;
        window.location.reload(false);
    }

    doNext() {
        localStorage.pc = Number(localStorage.pc) - 1;
        window.location.reload(false);
    }

    render() {
        if (!localStorage.getItem('login')) {
            return <Redirect to='/'/>;
        }

        const steps = [
            {
            selector: '[data-tour="claim_text"]',
            content: "Begin by reading the claim."
            },
            {
            selector: '[data-tour="question_view"]',
            content: "Read the question-answer pairs supplied by your fellow annotators."
            },
            {
            selector: '[data-tour="verdict"]',
            content: "Give your verdict for the claim. Do not use prior knowledge you may have, or information from elsewhere on the internet - give your verdict based ONLY on the question-answer pairs."
            },
            {
            selector: '[data-tour="justification"]',
            content: "Write a short explanation explaining how you decided the answer based on the question-answer pairs."
            },
            {
            selector: '[data-tour="unreadable"]',
            content: "If the claim lacks context or is otherwise not understandable, please report it rather than giving a label. If you do so, please use the justification field to explain why the question cannot be understood."
            },
            {
            selector: '[data-tour="report_question_problems"]',
            content: "If there are any problems with a question, please report it. If you report a question, please DO NOT use the information in that question-answer pair to give your verdict."
            },
            {
            selector: '[data-tour="report_answer_problems"]',
            content: "Similarly, if there are any problems with an answer, please report it. If you report an answer, please DO NOT use the information in that answer to give your verdict. You can still use any other answers provided for the question."
            },
            {
            selector: '[data-tour="submit"]',
            content: "When you have verified the claim, submit your verdict and proceed to the next article."
            },
        ];

        console.log(this.state.claim)
        console.log(localStorage.pc)

        const questionPairs = Object.keys(this.state.claim.questions).map(question_id => (
            <EntryCard variant="outlined">
                <StaticQuestionEntryField id={question_id} data={{"answers": [{}, ]}} question={this.state.claim.questions[question_id]} onChange={this.handleFieldChange}/>
            </EntryCard>
          ));


        var current_idx = 15-Number(localStorage.pc);
        var final_idx = 15;

        return (
            <div>
              <TourProvider steps={steps}>
              <RightBox>
                <RightPhaseControl current_idx={current_idx} final_idx={final_idx} phaseName="Verdict Validation" phaseInstructions="Please read the claim and the question-answer pairs. Then, give your verdict on the claim. Do not look at any external information; make your verdict based ONLY on the question-answer pairs. If there are any problems with a question-answer pair, please use the form to report it. Do not use the information in any question-answer pair you report to make your verdict."/>
              </RightBox>
                <LeftBox>
                  <ValidationClaimTopField claim={this.state.claim} valid={this.state.valid} data={this.state.annotation} ask_for_justification onChange={this.handleFieldChange} id="annotation"/>
                </LeftBox>
                <QABox >
                  <div data-tour="question_view">
                    {questionPairs}
                  </div>
                    <NavBar onPrevious={this.doPrevious} onSubmit={this.doSubmit} onNext={this.doNext}/>
                </QABox>
                {this.state.userIsFirstVisiting? <TourWrapper/> : ""}
                </TourProvider>
            </div>
        );
      }
}

export default VerdictValidation;
