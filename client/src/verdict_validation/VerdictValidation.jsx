import React from 'react';
import ClaimTopField from '../averitec_components/ClaimTopField';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';

const EntryCard = styled(Card)`
  margin:10px;
`

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
                questions: [
                    {
                        text: "is this a question?",
                        answer_type: "Abstractive",
                        answer: "this is definitely 100% a legit question.",
                        url: "www.abc.def/ghi"
                    },
                    {
                        text: "is this also question?",
                        answer_type: "Abstractive",
                        answer: "this is definitely 100% not a legit question.",
                        url: "www.abc.def/asdfasdf"
                    }
                ]
            },
            entries:{}
        }

        this.handleFieldChange = this.handleFieldChange.bind(this);
      }

      handleFieldChange(fieldId, element, value) {
          
          this.setState(prevState => ({
            entries: {
                  ...prevState.entries,
                  [fieldId]: {
                      ...prevState.entries[fieldId],
                      [element]: value
                  }
              }
          })) 
        }

    render() {
        const questionPairs = this.state.claim.questions.map(question => (
            <EntryCard variant="outlined">
                {question.text}
                <StaticQuestionEntryField question={question}/>
            </EntryCard>
          ));

        return (
            <div>
                <ClaimTopField claim={this.state.claim} ask_for_justification onChange={this.handleFieldChange} id="annotation"/>
                {questionPairs}
                <div>{JSON.stringify(this.state)}</div>
            </div>
        );
      }
}

export default VerdictValidation;