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
import CountryPickerWithTooltip from '../components/CountryPickerWithTooltip';
import PhaseControl from '../averitec_components/PhaseControl';

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
    padding-bottom:24px;
`

const TextLeftEntryDiv = styled.div`
  float:left;

  @media (max-width: 1674px)  {
    margin: 0px 0px 0px -webkit-calc(50% - 140px)!important;
    margin: 0px 0px 0px    -moz-calc(50% - 140px)!important;
    margin: 0px 0px 0px         calc(50% - 140px)!important;
  }

  @media (min-width: 1675px)  {
    width: 310px;
    margin: 0px -webkit-calc((100% - 620px)/3) 0px -webkit-calc((100% - 620px)/3)!important;
    margin: 0px -moz-calc((100% - 620px)/3) 0px    -moz-calc((100% - 620px)/3)!important;
    margin: 0px calc((100% - 620px)/3) 0px         calc((100% - 620px)/3)!important;
  }
`

const TextRightEntryDiv = styled.div`
  float:left;

  @media (max-width: 1674px)  {
    margin: -5px 0px 0px -webkit-calc(50% - 140px)!important;
    margin: -5px 0px 0px    -moz-calc(50% - 140px)!important;
    margin: -5px 0px 0px         calc(50% - 140px)!important;
  }
`

const CheckboxLeftEntryDiv = styled.div`
  float:left;

  @media (max-width: 1776px)  {
    margin: 0px -webkit-calc(50% - 134px)!important;
    margin: 0px    -moz-calc(50% - 134px)!important;
    margin: 0px         calc(50% - 134px)!important;
  }

  @media (min-width: 1777px)  {
    margin: 0px 0px 0px -webkit-calc(33% - 185px)!important;
    margin: 0px 0px 0px    -moz-calc(33% - 185px)!important;
    margin: 0px 0px 0px         calc(33% - 185px)!important;
  }
`

const CheckboxRightEntryDiv = styled.div`
  float:left;

  @media (max-width: 1776px)  {
    margin: 24px 0px 0px -webkit-calc(50% - 134px)!important;
    margin: 24px 0px 0px    -moz-calc(50% - 134px)!important;
    margin: 24px 0px 0px         calc(50% - 134px)!important;
  }

  @media (min-width: 1777px)  {
    margin: 0px 0px 0px -webkit-calc(33% - 147px)!important;
    margin: 0px 0px 0px    -moz-calc(33% - 147px)!important;
    margin: 0px 0px 0px         calc(33% - 147px)!important;
  }
`

const ClaimEntryDiv = styled.div`
  @media (min-width: 1777px)  {
    width: 310px;
  }
  float:left;
`

const ClaimReminderDiv = styled.div`
  margin: 5px 0px 0px 0px;

  width:280px;

  @media (max-width: 1674px)  {
    margin: 15px 0px 0px -webkit-calc(50% - 140px)!important;
    margin: 15px 0px 0px    -moz-calc(50% - 140px)!important;
    margin: 15px 0px 0px         calc(50% - 140px)!important;
  }

  @media (min-width: 1770px)  {
    width:310px;
  }
  float:left;
`

const FirstProperlySizedDivider = styled(Divider)`
  margin: -5px -15px -15px 45px!important;
`

const ProperlySizedDivider = styled(Divider)`
  margin: 0px -15px 0px 45px!important;
`

const VerdictBoxDiv = styled.div`
  margin: -10px 0px 0px 0px;
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
              <ClaimGrid container direction="column" justifyContent="center" alignItems="center" spacing={3}>
                <ClaimGridElement item xs>
                <ColumnDiv>
                <TextLeftEntryDiv>
                <ClaimEntryDiv>
                <TextFieldWithTooltip data-tour="claim_textfield" validator={notEmptyValidator} valid={this.props.valid} required multiline rows={6} value={this.props.data["cleaned_claim"]} name='cleaned_claim' label="Claim" onChange={this.handleFieldChange} tooltip="The text of the claim. Please verify that the claim has been copied correctly from the article below, and that it could be understood without reading the article."/>
                <VerdictBoxDiv data-tour="verdict">
                <SelectWithTooltip  validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["phase_1_label"]} name="phase_1_label" label="Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip={
                <ul>
                <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
                </ul>}/>
                </VerdictBoxDiv>
                </ClaimEntryDiv>
                </TextLeftEntryDiv>
                <ClaimReminderDiv>
                  When entering a claim, please ensure that:
                  <ul>
                    <li>Any references to people, places, or organizations can be understood <b>without</b> reading the article.</li>
                    <li>The claim directly mentions the speaker <b>only if</b> verifying that the speaker actually said the statement is a part of the task.</li>
                    <li>The claim is phrased as a statement, rather than a question.</li>
                  </ul>
                </ClaimReminderDiv>
                
                </ColumnDiv>
                </ClaimGridElement><FirstProperlySizedDivider orientation="horizontal" flexItem />
                <ClaimGridElement  data-tour="metadata_fields" item xs>
                <ColumnDiv>
                <TextLeftEntryDiv>
                <DatePickerWithTooltip name="date" label="Claim Date" onChange={this.handleFieldChange} tooltip="The date the original claim was made, if mentioned by the fact checking article."/>
                <TextFieldWithTooltip name='hyperlink' label="Hyperlink" onChange={this.handleFieldChange} tooltip="A hyperlink to the original claim, if that is provided by the fact checking site. If the original claim has a hyperlink on the fact checking site, but that hyperlink is dead, please leave the field empty."/>
                <TextFieldWithTooltip name='speaker' label="Speaker" onChange={this.handleFieldChange} tooltip="The speaker (or source) of the original claim."/>
                </TextLeftEntryDiv>
                <TextRightEntryDiv>                
                <TextFieldWithTooltip name='transcription' label="Transcription" onChange={this.handleFieldChange} tooltip="If the original source is an image that contains text (for example a meme or image macro), please transcribe whatever text occurs in the image here."/>
                <TextFieldWithTooltip name='media_source' label="Media Source URLs" onChange={this.handleFieldChange} tooltip="If the claim refers directly to an image, video, or audio file, please paste the link here. If multiple sources are referred to, please add them all, separated by commas."/>
                <CountryPickerWithTooltip name="location" label="Location" onChange={this.handleFieldChange} tooltip="Please select the location most relevant to the claim."/>


                </TextRightEntryDiv>
                
                
                </ColumnDiv>
                </ClaimGridElement><ProperlySizedDivider orientation="horizontal" flexItem />
              <ClaimGridElement  data-tour="metadata_fields_2" item xs>
              <ColumnDiv>
                <CheckboxLeftEntryDiv>
                <AtLeastOneCheckboxGroup 
                name="claim_types" 
                label="Claim Type" 
                data={this.props.data["claim_types"]}
                valid={this.props.valid}
                validator={atLeastOneValidator}
                required
                items={[
                  {label: "Speculative Claim", tooltip: "The primary task is to assess whether a prediction is plausible or realistic. For example \"the price of crude oil will rise next year.\""},
                  {label: "Opinion Claim", tooltip: "The claim is a non-factual opinion, e.g. \"cannabis should be legalized\". Note that this is distinct from causal claims, e.g. \"the legalization of cannabis would/has helped reduce opiod deaths\", and position claims, e.g. \"Julius Caesar believed cannabis should be legalized\"."},
                  {label: "Causal Claim", tooltip: "The primary task is to assess whether one thing caused another. For example \"the price of crude oil rose because of the Suez blockage.\""},
                  {label: "Position Statement", tooltip: "The primary task is to identify whether a public figure has taken a certain position, e.g. supporting a particular policy or idea."},
                  {label: "Numerical Claim", tooltip: "The primary task is to verify whether a numerical fact is true, to verify whether a comparison between several numerical facts hold, or to determine whether a numerical trend or correlation is supported by the evidence."},
                  {label: "Quote Verification", tooltip: "The primary task is to identify whether a quote was actually said by the supposed speaker."},
                  {label: "Event/Property Claim", tooltip: "The primary task is to determine the veracity of a narrative about a particular event or series of events, or to identify whether a certain non-numerical property is true, e.g. a person attending a particular university."},
                  {label: "Media Matching Claim", tooltip: "The primary task is to determine whether an image, video, or soundbite is presented in the right context. This includes doctored media as well as media taken out of context. This also includes HTML-doctoring of social media posts."},
                  {label: "Complex Media Claim", tooltip: "The primary task is to perform complex reasoning about pieces of media, distinct from doctoring. This could for example be geolocating an image, or analysing audio."}
                ]} 
                onChange={this.handleFieldChange}
                tooltip="Please determine the type of the claim itself, independent of the approach taken by the fact checker to verify or refute it. Check any that apply."
                />
                </CheckboxLeftEntryDiv>
                <CheckboxRightEntryDiv>
                <AtLeastOneCheckboxGroup 
                name="fact_checker_strategy" 
                label="Fact Checking Strategies" 
                data={this.props.data["fact_checker_strategy"]}
                valid={this.props.valid}
                validator={atLeastOneValidator}
                required
                items={[
                  {label: "Written Evidence", tooltip: "The fact checking process involved finding contradicting written evidence, e.g. a news article directly refuting the claim."},
                  {label: "Numerical Comparison", tooltip: "The fact checking process involved numerical comparisons, such as verifying that one number is greater than another."},
                  {label: "Consultation", tooltip: "The fact checkers directly reached out to relevant experts or people involved with the story, reporting new information from such sources as part of the fact checking article."},
                  {label: "Satirical Source Identification", tooltip: "The fact checking process involved identifying the source of the claim as satire, e.g. The Onion. We will discard all claims that were refuted only through satirical source identification."},
                  {label: "Media Source Discovery", tooltip: "The fact checking process involved finding the original source of a (potentially doctored) image, video, or soundbite."},
                  {label: "Image Analysis", tooltip: "The fact checking process involved image analysis other than finding the original source of an image, such as comparing two images."},
                  {label: "Other Non-text Media Analysis", tooltip: "The fact checking process involved analysing other media, such as video or audio."}
                ]} 
                onChange={this.handleFieldChange}
                tooltip="Please determine the approach taken by the fact checker, independent of the type of the claim. Check any that apply."
                />
                </CheckboxRightEntryDiv>
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
      <PhaseControl phaseName="Claim Normalization" phaseInstructions="Please read the fact checking article to the left, then fill out the information about the discussed claim below. If the article discusses more than one claim, you can add additional entry boxes for each claim. When entering a claim, please make sure that it can be understood without reading the article - if necessary, you can add context to the claim to ensure this." reportButton={true}/>
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