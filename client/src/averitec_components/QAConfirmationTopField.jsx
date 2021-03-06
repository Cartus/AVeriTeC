import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import { notEmptyValidator } from '../utils/validation.js'
import FormControlLabel from '@mui/material/FormControlLabel';
import Checkbox from '@mui/material/Checkbox';
import countryFlagEmoji from "country-flag-emoji";
import { Button } from '@material-ui/core';

const EntryCard = styled(Card)`
  margin:10px;
  @media (min-width: 1290px)  {
    height:260px;
  }
`

const ClaimHeader = styled.h4`
  text-align: center;
  @media  (max-width: 880px)  {
    margin: 10px -webkit-calc(50% - 130px)!important;
    margin: 10px    -moz-calc(50% - 130px)!important;
    margin: 10px         calc(50% - 130px)!important;
  }
  @media (min-width: 881px)  {
    margin: 10px!important;
  }
`

const ContainerDiv = styled.div`
    width:100%;
`

const TextEntryDiv = styled.div`
  float:left;

  
  @media  (max-width: 630px)  {
    padding: 20px 0px 0px -webkit-calc((100% - 305px)/2)!important;
    padding: 20px 0px 0px    -moz-calc((100% - 305px)/2)!important;
    padding: 20px 0px 0px         calc((100% - 305px)/2)!important;
  }
  @media (min-width: 631px)  {
    padding: 20px 0px 0px -webkit-calc((100% - 610px)/3)!important;
    padding: 20px 0px 0px    -moz-calc((100% - 610px)/3)!important;
    padding: 20px 0px 0px         calc((100% - 610px)/3)!important;
  }

`
const SepSpaceDiv = styled.div`
    padding: 10px 0px 0px 0px;
`

const SingleTextEntryDiv = styled.div`
  float:left;

  padding: 20px 0px 0px -webkit-calc((100% - 305px)/2)!important;
  padding: 20px 0px 0px    -moz-calc((100% - 305px)/2)!important;
  padding: 20px 0px 0px         calc((100% - 305px)/2)!important;

`

const SubmitButton = styled(Button)`
  float:left;
  width:130px;
  margin: 15px 5px 10px 5px!important;
`

class QAConfirmationClaimTopField extends React.Component {
  constructor(props) {
    super(props);

  }

  render() {
    console.log(this.props)
    return (
      <EntryCard>
        <ContainerDiv>
          <ClaimHeader>{this.props.claim_text}</ClaimHeader>
          <TextEntryDiv>
                    <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" value={this.props.claim.claim_speaker} defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The person or organization that said or wrote the original claim."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_source' label="Claim Source" value={this.props.claim.claim_source} defaultValue={this.props.claim.claim_source} InputProps={{readOnly: true}} variant="filled" tooltip="The source that published the original claim."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_date' label="Claim Date" value={this.props.claim.claim_date} InputProps={{readOnly: true}} variant="filled" tooltip="The date the original claim was made."/>
                    <SepSpaceDiv/>
          </TextEntryDiv>
          <TextEntryDiv>
            <SelectWithTooltip name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.label} label="Claim Label" onChange={this.props.changeLabel} items={["Supported", "Refuted", "Not Enough Evidence", "Conflicting Evidence/Cherrypicking"]} tooltip={
              <ul>
                <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                <li>Not Enough Evidence: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                <li>Conflicting Evidence/Cherrypicking: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
              </ul>}
            />
            <SubmitButton variant="contained" color="secondary" onClick={this.props.cancelFunction}>
              Cancel
            </SubmitButton>
            <SubmitButton variant="contained" color="primary" onClick={this.props.confirmFunction}>
              Confirm
            </SubmitButton>
          </TextEntryDiv>
        </ContainerDiv>
      </EntryCard>
    );
  }
}

export default QAConfirmationClaimTopField;