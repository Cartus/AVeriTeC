
import React from 'react';
import EntryCardContainer from '../components/EntryCardContainer';
import ClaimTopField from '../averitec_components/ClaimTopField';
import QuestionEntryField from '../averitec_components/QuestionEntryField';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'

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
            valid = false;
          }
        });
  
    return valid;
  }

class QuestionGenerationBar extends React.Component {

    render() {
        return (
            <EntryCardContainer 
            headerClass={ClaimTopField}
            contentClass={QuestionEntryField} 
            entryName="qa_pair" 
            addTooltip="Add another question."
            numInitialEntries={1}
            claim={this.props.claim}
            validationFunction={validate}
            />
        );
      }
}

export default QuestionGenerationBar