import React from 'react';
import AtLeastOneCheckboxGroup from "../components/AtLeastOneCheckboxGroup"
import SelectWithTooltip from "../components/SelectWithTooltip"
import DatePickerWithTooltip from '../components/DatePickerWithTooltip';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import EntryCardContainer from '../components/EntryCardContainer';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'

const ColumnDiv = styled.div`
    width:100%;
    margin: 20px;
`

const ClaimGrid = styled(Grid)`
    float: left;
    width: -webkit-calc(100% - 16px)!important;
    width:    -moz-calc(100% - 16px)!important;
    width:         calc(100% - 16px)!important;
`

const ClaimGridElement = styled(Grid)`
    width: 100%;
`

class ClaimEntryField extends React.Component {
    constructor(props) {
        super(props);

        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.handleDelete = this.handleDelete.bind(this)
    }

    handleFieldChange = event => {
        const { name, value } = event.target;
        this.props.onChange(this.props.id, name, value);
    }

    handleDelete = () => {
      this.props.onDelete(this.props.id)
    }

    render() {
        return (
            <div>
              <ClaimGrid container direction="column" justifyContent="space-evenly" alignItems="center" spacing={3}>
                <ClaimGridElement item xs>
                <ColumnDiv>
                <TextFieldWithTooltip name='hyperlink' label="Hyperlink" onChange={this.handleFieldChange} tooltip="A hyperlink to the original claim, if that is provided by the fact checking site. Examples of this include Facebook posts, the original article or blog post being fact checked, and embedded video links. If the original claim has a hyperlink on the fact checking site, but that hyperlink is dead, annotators should leave the field empty."/>
                <TextFieldWithTooltip validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["cleaned_claim"]} name='cleaned_claim' label="Claim" onChange={this.handleFieldChange} tooltip="Please verify that the claim has been copied correctly from the article below, and that it can be understood without the context of the article."/>
                <DatePickerWithTooltip name="date" label="Claim Date" onChange={this.handleFieldChange} tooltip="The date of the original claim, regardless of whether it is necessary for verifying the claim. This date is often mentioned by the fact checker, but not in a standardized place where we could automatically retrieve it. Note that the date of origin for the original claim and the fact checking article may be different and both stated in text. We specifically need the original claim date, as we intend to filter out results published after that date during search. Furthermore, that date may be necessary for checking the claim."/>
                <TextFieldWithTooltip name='speaker' label="Speaker" onChange={this.handleFieldChange} tooltip="The speaker (or source) of the original claim."/>
                <TextFieldWithTooltip name='transcription' label="Transcription" onChange={this.handleFieldChange} tooltip="If the original source is an image that contains text (for example, the Facebook meme about Michelle Obama listed above), we ask the annotators to transcribe whatever text occurs in the image as metadata. This is an easy way to add additional training data for anyone wishing to build models without an image processing component, and should not take much extra time for the annotators to gather."/>

                <SelectWithTooltip validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["phase_1_label"]} name="phase_1_label" label="Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip="
                <ul>
                <li>Supported: The claim is fully supported by the arguments and evidence presented.
                <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.
                <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.
                <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found. Missing context may also be relevant if a situation has changed over time, and the claim fails to mention this.</ul>"
                />
                </ColumnDiv>
                </ClaimGridElement><Divider orientation="horizontal" flexItem />
              <ClaimGridElement item xs>
              <ColumnDiv>
                <AtLeastOneCheckboxGroup 
                name="claim_types" 
                label="Claim Type" 
                data={this.props.data["claim_types"]}
                valid={this.props.valid}
                validator={atLeastOneValidator}
                required
                items={[
                  {label: "Speculative Claim", tooltip: "For example \"the price of crude oil will rise next year.\" The primary task is to assess whether the prediction is plausible or realistic."},
                  {label: "Numerical Claim", tooltip: "For example \"cannabis should be legalized\". This contrasts with factual claims on the same topic, such as \"legalization of cannabis has helped reduce opioid deaths.\""},
                  {label: "Position Statement", tooltip: "The primary task is to verify whether a numerical fact is true, to verify whether a comparison between several numerical facts hold, or to determine whether a numerical trend or correlation is supported by the evidence."},
                  {label: "Quote Verification", tooltip: "The primary task is to identify whether a quote was actually said by the supposed speaker."},
                  {label: "Position Statement", tooltip: "The primary task is to identify whether a public figure has taken a certain position, e.g. supporting a particular policy or idea."},
                  {label: "Event/Property Claim", tooltip: "The primary task is to determine the veracity of a narrative about a particular event or series of events, or to identify whether a certain non-numerical property is true, e.g. a person attending a particular university."},
                  {label: "Doctored Media Identification", tooltip: "The primary task is to determine whether an image, video, or soundbite has been doctored. This also includes HTML-doctoring of social media posts."},
                  {label: "Complex Media Claim", tooltip: "The primary task is to perform complex reasoning about pieces of media, distinct from doctoring. This could for example be geolocating an image, or analysing audio."}
                ]} 
                onChange={this.handleFieldChange}
                tooltip="The type of the claim itself, independent of the approach taken by the fact checker to verify or refute it."
                />
              </ColumnDiv>
              </ClaimGridElement><Divider orientation="horizontal" flexItem />
              <ClaimGridElement item xs>
              <ColumnDiv>
                <AtLeastOneCheckboxGroup 
                name="fact_checker_strategy" 
                label="Fact Checking Strategy" 
                data={this.props.data["fact_checker_strategy"]}
                valid={this.props.valid}
                validator={atLeastOneValidator}
                required
                items={[
                  {label: "Written Evidence", tooltip: "The fact checking process involved finding contradicting written evidence, e.g. a news article directly refuting the claim."},
                  {label: "Numerical Comparison", tooltip: "The fact checking process involved numerical comparisons, such as verifying that one number is greater than another."},
                  {label: "Consultation", tooltip: "The fact checkers directly reached out to relevant experts or people involved with the story, reporting new information from such sources as part of the fact checking article."},
                  {label: "Satirical Source Identification", tooltip: "The fact checking process involved identifying the source of the claim as satire, e.g. The Onion. We will discard all claims that were refuted only through satirical source identification."},
                  {label: "Image Analysis", tooltip: "The fact checking process involved image analysis, such as comparing two images."},
                  {label: "Other Media Analysis", tooltip: "The fact checking process involved analysing other media, such as video."}
                ]} 
                onChange={this.handleFieldChange}
                tooltip="The approach taken by the fact checker, independent of the type of the claim."
                />
              </ColumnDiv>
              </ClaimGridElement>
            </ClaimGrid>
            </div>
        );
      }
}

function validate(content){
  var valid = true

  Object.values(content["entries"]).forEach(entry =>
    {
      if(!("fact_checker_strategy" in entry) || atLeastOneValidator(entry["fact_checker_strategy"]).error){
        valid = false;
      } else if(!("claim_types" in entry) || atLeastOneValidator(entry["claim_types"]).error){
        valid = false;
      } else if(!("phase_1_label" in entry) || notEmptyValidator(entry["claim_types"]).error){
        valid = false;
      }
    });

  return valid;
}

function MetadataEntryBar({className}) {
  return(
    <div className={className}>
      <EntryCardContainer 
      contentClass={ClaimEntryField} 
      entryName="claim" 
      addTooltip="Add another claim. Only do so if the article fact checks more than one claim, or a claim consisting of parts that are checked independently."
      numInitialEntries={1}
      validationFunction={validate}
      />
    </div>
  );
} 

export default MetadataEntryBar;