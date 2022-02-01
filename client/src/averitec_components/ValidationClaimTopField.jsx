import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import { notEmptyValidator } from '../utils/validation.js'
import FormControlLabel from '@mui/material/FormControlLabel';
import Checkbox from '@mui/material/Checkbox';
import countryFlagEmoji from "country-flag-emoji";

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

const SepSpaceDiv = styled.div`
    padding: 10px 0px 0px 0px;
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

const CheckboxBox = styled(FormControlLabel)`
  width: 300px;
  margin: 20px 5px;
`


class ClaimTopField extends React.Component {
  constructor(props) {
    super(props);

    this.handleFieldChange = this.handleFieldChange.bind(this);
    this.handleCheckboxChange = this.handleCheckboxChange.bind(this);
    this.handleDelete = this.handleDelete.bind(this)
  }

  handleFieldChange = event => {
    const { name, value } = event.target;
    this.props.onChange(this.props.id, name, value);
  }

  handleCheckboxChange = event => {
    this.props.onChange(this.props.id, event.target.name, event.target.checked);
    this.props.onChange(this.props.id, "label", "");
  }

  handleDelete = () => {
    this.props.onDelete(this.props.id)
  }

  render() {
    let justification = ""

    if (!this.props.hide_justification) {
      justification = <TextEntryDiv >
        <TextFieldWithTooltip
          name='justification'
          data-tour="justification"
          label="Justification"
          validator={notEmptyValidator}
          valid={this.props.valid}
          value={this.props.data["justification"]}
          required
          multiline
          rows={5}
          onChange={this.handleFieldChange}
          InputProps={this.props.posthocView ? { readOnly: true } : undefined} 
          variant={this.props.posthocView ? "filled" : undefined}
          tooltip="Please write a short explanation for how you decided the answer based on the questions."
        />
      </TextEntryDiv>
    }

    var location = ""
    if (this.props.claim.country_code) {
      var country_list = countryFlagEmoji.list.filter((entry) => { return !["European Union", "United Nations"].includes(entry.name) }).sort((a, b) => {
        var textA = a.name.toUpperCase();
        var textB = b.name.toUpperCase();
        return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
      });

      var country_by_code_dict = country_list.reduce((a, x) => ({ ...a, [x.code]: x }), {})
      location = country_by_code_dict[this.props.claim.country_code].name + " (" + country_by_code_dict[this.props.claim.country_code].code + ")"
    }

    return (
      <EntryCard>
        <ContainerDiv>

          <ClaimHeader data-tour="claim_text">{this.props.claim.claim_text}</ClaimHeader>
          <TextEntryDiv>
            <div data-tour="verdict">
              {this.props.data["unreadable"] ?

                <SelectWithTooltip readOnly={this.props.posthocView} name="label" disabled value={this.props.data["label"]} label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip={
                  <ul>
                    <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                    <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                    <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                    <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
                  </ul>}
                />

                :

                <SelectWithTooltip readOnly={this.props.posthocView} name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["label"]} label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip={
                  <ul>
                    <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                    <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                    <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                    <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
                  </ul>}
                />

              }
            </div>

            <CheckboxBox data-tour="unreadable" control={<Checkbox name="unreadable" checked={this.props.data["unreadable"] ? this.props.data["unreadable"] : false} onChange={this.props.posthocView ? () => { } : this.handleFieldChange} />} label="The claim is vague, unreadable, or otherwise impossible to understand." />

          </TextEntryDiv>
          {justification}

        </ContainerDiv>
      </EntryCard>
    );
  }
}

export default ClaimTopField;