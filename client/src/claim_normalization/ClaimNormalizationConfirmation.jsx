import React from 'react';
import ValidationClaimTopField from '../averitec_components/ValidationClaimTopField';
import styled from 'styled-components';
import NavBar from '../averitec_components/NavBar';
import PhaseControl from '../averitec_components/PhaseControl';
import Card from '@material-ui/core/Card';
import QAConfirmationTopField from '../averitec_components/QAConfirmationTopField';
import StaticQuestionEntryField from '../averitec_components/StaticQuestionEntryField';
import { Button } from '@material-ui/core';

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

const TopBox = styled.div`
  width:100%;
  float: right;
  margin: 0px -10px 0px 0px;
`

const QABox = styled.div`
  width: 100%;
  float: left;
  margin: -10px 0px 0px 0px;
`

const PhaseHeader = styled.h4`
    margin: 10px 10px;
    float: left;
    width: 100%;
`

const SubmitButton = styled(Button)`
  float:right;
  width:130px;
  margin: 15px 5px 10px 5px!important;
`

const CheckDiv = styled.div`
    margin: 10px 10px;
    float: left;
    width: 95%;
`


class ClaimNormalizationConfirmation extends React.Component {
    constructor(props) {
        super(props);

    }

    render() {
        const questionPairs = Object.keys(this.props.entries).map(claim_id => (
          <div>
          {this.props.entries[claim_id].transcription 
          || (this.props.entries[claim_id].claim_types 
            && (this.props.entries[claim_id].claim_types.includes("Speculative Claim") || this.props.entries[claim_id].claim_types.includes("Opinion Claim"))) 
          || (this.props.entries[claim_id].fact_checker_strategy 
            && this.props.entries[claim_id].fact_checker_strategy.includes("Media Source Discovery"))?
            <EntryCard variant="outlined">
              <PhaseHeader>{this.props.entries[claim_id].cleaned_claim}</PhaseHeader>

              {
                this.props.entries[claim_id].transcription?
                <CheckDiv>You have entered the following <b>transcription</b>: "{this.props.entries[claim_id].transcription}". Please confirm that the original source involves an image that contains text (for example a meme or image macro), and that it contains <b>exactly</b> the text you have transcribed. For example, for <a href="https://cdn.broadbandsearch.net/images/blogs/most-popular-internet-memes-in-history/ceiling-cat.jpg">this</a> image, the transcription should be "ceiling cat is watching you".</CheckDiv>
                :
                ""
              }

              {
                this.props.entries[claim_id].claim_types && this.props.entries[claim_id].claim_types.includes("Speculative Claim")?
                <CheckDiv>You have selected the claim type <b>Speculative Claim</b>. Please confirm that the primary task for this claim is to assess whether a prediction is plausible or realistic. For example, "the price of crude oil will rise next year."</CheckDiv>
                :
                ""
              }

              {
                this.props.entries[claim_id].claim_types && this.props.entries[claim_id].claim_types.includes("Opinion Claim")?
                <CheckDiv>You have selected the claim type <b>Opinion Claim</b>. Please confirm that claim is a non-factual opinion, e.g. "cannabis should be legalized". Note that this is distinct from Causal Claims, e.g. "the legalization of cannabis would/has helped reduce opiod deaths", and Position Claims, e.g. "Julius Caesar believed cannabis should be legalized".</CheckDiv>
                :
                ""
              }

              {
                this.props.entries[claim_id].fact_checker_strategy && this.props.entries[claim_id].fact_checker_strategy.includes("Media Source Discovery")?
                <CheckDiv>You have selected the claim type <b>Media Source Discovery</b>. Please confirm that the fact-checking process involved finding the original source of a (potentially doctored) <b>image, video, or soundbite</b>. An example can be seen <a href="https://www.indiatoday.in/fact-check/story/old-videos-go-viral-visuals-telangana-rains-lash-state-1731843-2020-10-15">here</a>, where a claim of a crocodile swimming through the streets of Hyderabad is fact-checked by identifying the original source as an older video from Vadodara. If the source is <b>text</b>, e.g. a social media post, the strategy is <b>not</b> media source discovery.</CheckDiv>
                :
                ""
              }
            </EntryCard>
            : ""
          }
          </div>
        ));

        return <div>
            <TopBox>
                <RightPhaseControl current_idx={this.props.current_idx} final_idx={this.props.final_idx} phaseName="Confirmation" phaseInstructions="Please confirm that the claim types and fact-checker strategies you have chosen (shown below) are accurate." />
                
            </TopBox>
            <QABox >
                <div>
                    {questionPairs}
                </div>
                
            <EntryCard>
              <SubmitButton variant="contained" color="primary" onClick={this.props.confirmFunction}>
              Confirm
              </SubmitButton>
              <SubmitButton variant="contained" color="secondary" onClick={this.props.cancelFunction}>
              Cancel
              </SubmitButton>
            </EntryCard>
            </QABox>
        </div>
    }

}

export default ClaimNormalizationConfirmation;