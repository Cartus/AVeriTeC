
import React from 'react';
import EntryCardContainer from '../components/EntryCardContainer';
import ClaimTopField from '../averitec_components/ClaimTopField';
import QuestionEntryField from '../averitec_components/QuestionEntryField';

class QuestionGenerationBar extends React.Component {

    render() {
        return (
            <EntryCardContainer 
            headerClass={ClaimTopField}
            contentClass={QuestionEntryField} 
            entryName="qa_pair" 
            addTooltip="Add another question."
            numInitialEntries={2}
            claim={this.props.claim}
            />
        );
      }
}

export default QuestionGenerationBar