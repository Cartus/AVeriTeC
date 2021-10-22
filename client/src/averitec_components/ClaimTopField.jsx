import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import {notEmptyValidator} from '../utils/validation.js'

const EntryCard = styled(Card)`
  margin:10px;
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
    padding: 5px 0px 0px 0px;
`

const EmptySpaceDiv = styled.div`
    width:100%;
    @media (max-width: 1674px)  {
        padding: 5px 0px 0px 0px;
        padding: 5px 0px 0px 0px;
        padding: 5px 0px 0px 0px;
    }
`

const TextLeftEntryDiv = styled.div`
  float:left;

  @media  (max-width: 1674px)  {
    margin: 20px 0px 0px -webkit-calc(50% - 140px)!important;
    margin: 20px 0px 0px    -moz-calc(50% - 140px)!important;
    margin: 20px 0px 0px         calc(50% - 140px)!important;
  }

  @media (min-width: 1675px)  {
    margin: 20px 0px 20px -webkit-calc((100% - 586px)/3)!important;
    margin: 20px 0px 20px    -moz-calc((100% - 586px)/3)!important;
    margin: 20px 0px 20px         calc((100% - 586px)/3)!important;
  }
`

const TextRightEntryDiv = styled.div`
  float:left;

  @media (max-width: 1674px)  {
    margin: -5px 0px 20px -webkit-calc(50% - 140px)!important;
    margin: -5px 0px 20px    -moz-calc(50% - 140px)!important;
    margin: -5px 0px 20px         calc(50% - 140px)!important;
  }

  @media (min-width: 1675px)  {
    margin: 20px 0px 20px -webkit-calc((100% - 586px)/3)!important;
    margin: 20px 0px 20px    -moz-calc((100% - 586px)/3)!important;
    margin: 20px 0px 20px         calc((100% - 586px)/3)!important;
  }
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
        return (
            <EntryCard>
                <ContainerDiv>

                <ClaimHeader>{this.props.claim.claim_text}</ClaimHeader>
                <TextLeftEntryDiv>
                    <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The speaker (or source) of the original claim."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_date' label="Claim Date" defaultValue={this.props.claim.claim_date} InputProps={{readOnly: true}} variant="filled" tooltip="The date the original claim was made."/>
                    <SepSpaceDiv/>
                </TextLeftEntryDiv>
                <TextRightEntryDiv>
                    <EmptySpaceDiv/>
                    <SelectWithTooltip name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["label"]} label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip={
                  <ul>
                  <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                  <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                  <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                  <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
                  </ul>}
                    />
                </TextRightEntryDiv>
                </ContainerDiv>
            </EntryCard>
        );
    }
}

export default ClaimTopField;