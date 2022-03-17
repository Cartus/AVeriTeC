import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import ClaimTopField from './ClaimTopField';
import SelectWithTooltip from '../components/SelectWithTooltip';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';

const EntryCard = styled(Card)`
  margin:10px;
  padding:10px;
`

const EntryDiv = styled.div`
  float:left;
  margin: 20px 0px 0px -webkit-calc(50% - 140px)!important;
  margin: 20px 0px 0px    -moz-calc(50% - 140px)!important;
  margin: 20px 0px 0px         calc(50% - 140px)!important;
`

const SepSpaceDiv = styled("div")`
  float:left;
  width:100%;
  margin: 20px 0px 0px 0px;
`

const TextDiv = styled("div")`
  float:left;
  width:100%;
`

export default function PhaseFourTopField(props) {

  return <div>
    <ClaimTopField
      {...props}
      posthocView={true}
    />
    <EntryCard>
      <TextDiv>
        There is a disagreement between the verdicts selected by the previous phase two and three annotators. The phase two annotator selected the following:
      </TextDiv>

      <EntryDiv>
        <SelectWithTooltip readOnly={true} name="label" valid={true} value={props.previous_label_data ? props.previous_label_data["phase_two_label"] : ""} label="Phase Two Label" items={["Supported", "Refuted", "Not Enough Evidence", "Conflicting Evidence/Cherrypicking"]} tooltip={
          <ul>
            <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
            <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
            <li>Not Enough Evidence: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
            <li>Conflicting Evidence/Cherrypicking: Both supporting and refuting evidence was found for this claim. This includes cherry-picking, i.e. true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
          </ul>}
        />
      </EntryDiv>
      <SepSpaceDiv />
      <TextDiv>
        The phase three annotator selected:
      </TextDiv>

      <EntryDiv>
        <SelectWithTooltip readOnly={true} name="label" valid={true} value={props.previous_label_data ? props.previous_label_data["phase_three_label"] : ""} label="Phase Three Label" items={["Supported", "Refuted", "Not Enough Evidence", "Conflicting Evidence/Cherrypicking"]} tooltip={
          <ul>
            <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
            <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
            <li>Not Enough Evidence: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
            <li>Conflicting Evidence/Cherrypicking: Both supporting and refuting evidence was found for this claim. This includes cherry-picking, i.e. true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
          </ul>}
        />
      </EntryDiv>
      <SepSpaceDiv />

      <TextDiv>
        The phase three annotator gave the following explanation for their verdict:
      </TextDiv>

      <EntryDiv>
        <TextFieldWithTooltip
          name='justification'
          data-tour="justification"
          label="Justification"
          value={props.previous_label_data["justification"]}
          multiline
          rows={5}
          InputProps={{ readOnly: true }}
          variant={"filled"}
          tooltip="A short explanation for how the phase three verdict was reached."
        /></EntryDiv>

    </EntryCard>
  </div>
}