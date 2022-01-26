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
    return (
      <EntryCard>
        <ContainerDiv>
          <ClaimHeader>{this.props.claim.claim_text}</ClaimHeader>
          <SingleTextEntryDiv>
            <SelectWithTooltip name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.label} label="Claim Label" onChange={this.props.changeLabel} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip={
              <ul>
                <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
              </ul>}
            />
            <SubmitButton variant="contained" color="secondary" onClick={this.props.cancelFunction}>
              Cancel
            </SubmitButton>
            <SubmitButton variant="contained" color="primary" onClick={this.props.confirmFunction}>
              Confirm
            </SubmitButton>
          </SingleTextEntryDiv>
        </ContainerDiv>
      </EntryCard>
    );
  }
}

export default QAConfirmationClaimTopField;