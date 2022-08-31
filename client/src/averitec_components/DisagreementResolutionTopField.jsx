import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import { notEmptyValidator } from '../utils/validation.js'
import Checkbox from '@mui/material/Checkbox';
import countryFlagEmoji from "country-flag-emoji";
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';

const EntryCard = styled(Card)`
  margin:10px;
  @media (min-width: 1290px)  {
    height:260px;
  }
`

const OtherClaimWarningDiv = styled.div`
  border-style: solid;
  border-color: red;
  border-width: 1px;
  
  margin: 20px 10px 0px 10px!important;
  padding: 10px!important;
`

const ClaimHeader = styled.h4`
  margin: 10px!important;
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

const RadioBoxBox = styled.div`
  margin: 0px 20px;
`


class DisagreementResolutionTopField extends React.Component {
  constructor(props) {
    super(props);

    this.handleFieldChange = this.handleFieldChange.bind(this);
  }

  handleFieldChange = event => {
    const { name, value } = event.target;
    console.log("OnChange " + this.props.id + "," + name + "," + value)
    this.props.onChange(this.props.id, name, value);
  }

  render() {
    let justification = <TextFieldWithTooltip
      name='justification'
      data-tour="justification"
      label="Justification"
      validator={notEmptyValidator}
      InputProps={{ readOnly: true }}
      variant="filled"
      valid={this.props.valid}
      value={this.props.claim["justification"]}
      multiline
      rows={4}
      onChange={this.handleFieldChange}
      tooltip="Please write a short explanation for how you decided the answer based on the questions."
    />


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

    var other_claims_field = ""
    if (this.props.claim && this.props.claim.other_extracted_claims && this.props.claim.other_extracted_claims.length > 0) {
      var other_claims_items = this.props.claim.other_extracted_claims.map(claim =>
        <li>
          {claim}
        </li>
      );

      other_claims_field = <OtherClaimWarningDiv>
        <font style={{ color: "red" }}>Please note:</font> This article was used to produce multiple claims in phase one. In addition to the one you have been assigned, the following claims were produced. Please do not change this claim to include any aspect covered in these other extracted claims.
        <ul>
          {other_claims_items}
        </ul>
      </OtherClaimWarningDiv>
    }

    return (
      <EntryCard>
        <ContainerDiv>

          <ClaimHeader data-tour="claim_text">{this.props.claim.claim_text}</ClaimHeader>
          {other_claims_field}
          <TextEntryDiv>
            <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" value={this.props.claim.claim_speaker} defaultValue={this.props.claim.claim_speaker} InputProps={{ readOnly: true }} variant="filled" tooltip="The person or organization that said or wrote the original claim." />
            <SepSpaceDiv />
            <TextFieldWithTooltip name='claim_source' label="Claim Source" value={this.props.claim.claim_source} defaultValue={this.props.claim.claim_source} InputProps={{ readOnly: true }} variant="filled" tooltip="The source that published the original claim." />
            <SepSpaceDiv />
            <TextFieldWithTooltip name='claim_date' label="Claim Date" value={this.props.claim.claim_date} InputProps={{ readOnly: true }} variant="filled" tooltip="The date the original claim was made." />
            <SepSpaceDiv />
          </TextEntryDiv>
          <TextEntryDiv >
            <TextFieldWithTooltip name='claim_location' label="Location" value={location} InputProps={{ readOnly: true }} variant="filled" tooltip="The location most relevant to the claim." />
            <SepSpaceDiv />
            {justification}
          </TextEntryDiv>
          <TextEntryDiv>
            <RadioBoxBox data-tour="verdict_choice">
              {(this.props.claim["phase_2_label"] && this.props.claim["phase_3_label"]) ?
                <FormControl component="fieldset">
                  <FormLabel required={true} component="legend">Preferred label:</FormLabel>
                  <RadioGroup
                    aria-label="label"
                    name="preferred_label"
                    value={this.props.data["preferred_label"] ? this.props.data["preferred_label"] : ""}
                    validator={notEmptyValidator}
                    onChange={this.handleFieldChange}
                  >
                    <FormControlLabel value={this.props.claim["phase_2_label"]} control={<Radio />} label={this.props.claim["phase_2_label"]} />
                    <FormControlLabel value={this.props.claim["phase_3_label"]} control={<Radio />} label={this.props.claim["phase_3_label"]} />
                  </RadioGroup>
                </FormControl>
                :
                ""}
            </RadioBoxBox>

          </TextEntryDiv>
        </ContainerDiv>
      </EntryCard>
    );
  }
}

export default DisagreementResolutionTopField;