import React from 'react';
import EntryCardContainer from '../components/PropEntryCardContainer';
import ClaimTopField from '../averitec_components/ClaimTopField';
import QuestionEntryField from '../averitec_components/QuestionEntryField';
import PhaseControl from '../averitec_components/PhaseControl';
import VerdictBar from '../averitec_components/VerdictBar';
import ReportBar from '../averitec_components/ReportBar';

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
            valid={this.props.valid}
            addEntry={this.props.addEntry}
            deleteEntry={this.props.deleteEntry}
            doSubmit={this.props.doSubmit}
            handleFieldChange={this.props.handleFieldChange}
            />
          </div>
        );
      }
}

export default QuestionGenerationBar