import React from 'react';
import ValidationClaimTopField from '../averitec_components/ValidationClaimTopField';
import styled from 'styled-components';
import NavBar from '../averitec_components/NavBar';
import PhaseControl from '../averitec_components/PhaseControl';
import Card from '@material-ui/core/Card';
import QAConfirmationTopField from '../averitec_components/QAConfirmationTopField';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';

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


class QuestionGenerationConfirmation extends React.Component {
    constructor(props) {
        super(props);

    }

    render() {
      let claim_text = this.props.claim.claim_text

      if (this.props.claim_correction){
        claim_text = this.props.claim_correction
      }

        const questionPairs = Object.keys(this.props.entries).map(question_id => (
            <EntryCard variant="outlined">
                <StaticQuestionEntryField id={question_id} data={this.props.entries[question_id]} onChange={this.handleFieldChange} hide_problem_checkboxes={true} />
            </EntryCard>
        ));

        return <div>
            <RightBox>
                <RightPhaseControl current_idx={this.props.current_idx} final_idx={this.props.final_idx} phaseName="Confirmation" phaseInstructions="Please confirm that you can infer your chosen verdict using ONLY your question-answer pairs (shown below)." />
            </RightBox>
            <LeftBox>
                <QAConfirmationTopField claim_text={claim_text} label={this.props.label} cancelFunction={this.props.cancelFunction} confirmFunction={this.props.confirmFunction} changeLabel={this.props.changeLabel} id="confirmation" />
            </LeftBox>
            <QABox >
                <div>
                    {questionPairs}
                </div>
            </QABox>
        </div>
    }

}

export default QuestionGenerationConfirmation;