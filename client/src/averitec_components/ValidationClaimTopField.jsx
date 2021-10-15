import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import {notEmptyValidator} from '../utils/validation.js'

const EntryCard = styled(Card)`
  margin:10px;

  @media (min-width: 1290px)  {
    height:230px;
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

const SepSpaceDiv = styled.div`
    padding: 10px 0px 0px 0px;
`

const TextEntryDiv = styled.div`
  float:left;

  padding: 20px 0px 0px -webkit-calc((100% - 890px)/4)!important;
  padding: 20px 0px 0px    -moz-calc((100% - 890px)/4)!important;
  padding: 20px 0px 0px         calc((100% - 890px)/4)!important;
`


class ClaimTopField extends React.Component {
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
        let justification = <TextFieldWithTooltip 
        name='justification' 
        label="Justification" 
        validator={notEmptyValidator} 
        valid={this.props.valid} 
        value={this.props.data["justification"]} 
        required multiline 
        rows={4} 
        inputProps={{ maxLength: 300 }}
        onChange={this.handleFieldChange} 
        tooltip="Please write a short explanation (max 300 characters) for how you decided the answer based on the questions."
        />

        return (
            <EntryCard>
                <ContainerDiv>

                <ClaimHeader>{this.props.claim.claim_text}</ClaimHeader>
                <TextEntryDiv>
                    <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The name of the person or organization who produced the claim"/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_date' label="Claim Date" defaultValue={this.props.claim.claim_date} InputProps={{readOnly: true}} variant="filled" tooltip="The date the claim was made"/>
                    <SepSpaceDiv/>
                </TextEntryDiv>
                <TextEntryDiv>
                  {justification}
                </TextEntryDiv>
                <TextEntryDiv>
                    <SelectWithTooltip name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["label"]} label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip="
                    <ul>
                    <li>Supported: The claim is fully supported by the arguments and evidence presented.
                    <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.
                    <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.
                    <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found. Missing context may also be relevant if a situation has changed over time, and the claim fails to mention this.</ul>"
                    />
                </TextEntryDiv>
                </ContainerDiv>
            </EntryCard>
        );
    }
}

export default ClaimTopField;