
import React from 'react';
import EntryCardContainer from '../components/EntryCardContainer';
import ClaimTopField from '../averitec_components/ClaimTopField';
import QuestionEntryField from '../averitec_components/QuestionEntryField';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'
import PhaseControl from '../averitec_components/PhaseControl';

function validate(content){
    var valid = true

    if(!("label" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["label"]).error){
        valid = false;
    }

    Object.values(content["entries"]).forEach(entry =>
        {
          if(!("question" in entry) || notEmptyValidator(entry["question"]).error){
            valid = false;
          } else if(!("answer" in entry) || notEmptyValidator(entry["answer"]).error){
            if (!("answer_type" in entry) || entry["answer_type"] != "Unanswerable"){
              valid = false;
            }
          } else if(!("answer_type" in entry) || notEmptyValidator(entry["answer_type"]).error){
            valid = false;
          } else if(!("source_url" in entry) || notEmptyValidator(entry["source_url"]).error){
            if (!("answer_type" in entry) || entry["answer_type"] != "Unanswerable"){
              valid = false;
            }
          }
        });
  
    return valid;
  }

class QuestionGenerationBar extends React.Component {

    render() {
        return (
          <div>
            <PhaseControl phaseName="Question Generation" reportButton={true} phaseInstructions="Please read the claim below, and the fact checking article to the left. Then, construct question-answer pairs using the boxes and the search field below to collect evidence from the internet. You can also use any links in the fact checking article to provide sources for your answers. If you cannot find an answer to a question you ask, please label that question &quot;Unanswerable&quot; and ask another question. When you have collected enough evidence to verify the claim independently of the fact checking article, please give your verdict. Please spend no more than N minutes on each claim."/>
            <EntryCardContainer 
            headerClass={ClaimTopField}
            contentClass={QuestionEntryField} 
            entryName="qa_pair" 
            addTooltip="Add another question. Only do so if you think the question-answer pairs you have already collected do not contain sufficient evidence to give a verdict for the claim."
            numInitialEntries={1}
            claim={this.props.claim}
            entries={this.props.entries}
            header={this.props.header}
            validationFunction={validate}
            />
          </div>
        );
      }
}

export default QuestionGenerationBar