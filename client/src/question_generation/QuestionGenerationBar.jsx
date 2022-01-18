import React from 'react';
import EntryCardContainer from '../components/EntryCardContainer';
import ClaimTopField from '../averitec_components/ClaimTopField';
import QuestionEntryField from '../averitec_components/QuestionEntryField';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'
import PhaseControl from '../averitec_components/PhaseControl';
import VerdictBar from '../averitec_components/VerdictBar';
import ReportBar from '../averitec_components/ReportBar';

function validate(content){
    var valid = true

    if(!("label" in content["qa_pair_footer"]) || notEmptyValidator(content["qa_pair_footer"]["label"]).error){
        console.log("no label");
        valid = false;
    }

    // if("should_correct" in content["qa_pair_header"] && (!("claim_correction" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["claim_correction"]).error)){
    // if(content["qa_pair_header"]["should_correct"] === 1 && (!("claim_correction" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["claim_correction"]).error)){
    //     console.log("no correction");
    //     valid = false;
    // }

    if("should_correct" in content["qa_pair_header"] && content["qa_pair_header"]["should_correct"] == true &&(!("claim_correction" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["claim_correction"]).error)){
        console.log("no correction");
        valid = false;
    }

    Object.values(content["entries"]).forEach(entry =>
        {
          if(!("question" in entry) || notEmptyValidator(entry["question"]).error){
            console.log("no question");
            valid = false;
          } 
          else if (!"answers" in entry){
            console.log("no answers");
            valid = false;
          } else{
            entry["answers"].forEach(answer => {
              if(!("answer_type" in answer) || notEmptyValidator(answer["answer_type"]).error){
                console.log("no answer type");
                valid = false;
              }

              if(!("source_url" in answer) || notEmptyValidator(answer["source_url"]).error){
                if (!("answer_type" in answer) || answer["answer_type"] != "Unanswerable"){
                  console.log("no source url and not unanswerable");
                  valid = false;
                }
              }

              if (answer["answer_type"] == "Boolean"){
                if (!("bool_explanation" in answer) ||  notEmptyValidator(answer["bool_explanation"]).error){
                  console.log("boolean and no expl");
                  valid = false;
                }
              }
            });
          }
        });

    console.log(valid);
  
    return valid;
  }

class QuestionGenerationBar extends React.Component {

    render() {
        return (
          <div>
            <PhaseControl current_idx={this.props.current_idx} final_idx={this.props.final_idx} phaseName="Question Generation" phaseInstructions="Please read the claim below, and the fact checking article to the left. Then, construct question-answer pairs. You can use any links in the fact checking article to provide sources for your answers. If the links in the fact-checking article are not sufficient, you can also use our custom search field below; please do not use any other search field. If you cannot find an answer to a question you ask, please label that question &quot;Unanswerable&quot; and ask another question. When you have collected enough evidence to verify the claim independently of the fact checking article, please give your verdict. Please spend no more than 10 minutes on each claim."/>
            <ReportBar/>
            <EntryCardContainer 
            headerClass={ClaimTopField}
            contentClass={QuestionEntryField} 
            footerClass={VerdictBar}
            entryName="qa_pair" 
            addTooltip="Add another question. Only do so if you think the question-answer pairs you have already collected do not contain sufficient evidence to give a verdict for the claim."
            numInitialEntries={1}
            claim={this.props.claim}
            entries={this.props.entries}
            header={this.props.header}
            footer={this.props.footer}
            validationFunction={validate}
            />
          </div>
        );
      }
}

export default QuestionGenerationBar