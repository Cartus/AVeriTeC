import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import {notEmptyValidator} from '../utils/validation.js'
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

  padding: 20px 0px 0px -webkit-calc((100% - 890px)/4)!important;
  padding: 20px 0px 0px    -moz-calc((100% - 890px)/4)!important;
  padding: 20px 0px 0px         calc((100% - 890px)/4)!important;
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
  }

    handleDelete = () => {
      this.props.onDelete(this.props.id)
    }

    render() {
        let justification = <TextFieldWithTooltip 
        name='justification' 
        data-tour="justification"
        label="Justification" 
        validator={notEmptyValidator} 
        valid={this.props.valid} 
        value={this.props.data["justification"]} 
        required 
        multiline 
        rows={4} 
        inputProps={{ maxLength: 300 }}
        onChange={this.handleFieldChange} 
        tooltip="Please write a short explanation (max 300 characters) for how you decided the answer based on the questions."
        />

        
      var location = ""
      if (this.props.claim.location){
        var country_list = countryFlagEmoji.list.filter((entry) => {return !["European Union", "United Nations"].includes(entry.name)}).sort( (a,b) => {
          var textA = a.name.toUpperCase();
          var textB = b.name.toUpperCase();
          return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
        });
      
        var country_by_code_dict = country_list.reduce((a,x) => ({...a, [x.code]: x}), {})
        location = country_by_code_dict[this.props.claim.location].name + " (" + country_by_code_dict[this.props.claim.location].code + ")"
      }

        return (
            <EntryCard>
                <ContainerDiv>

                <ClaimHeader data-tour="claim_text">{this.props.claim.claim_text}</ClaimHeader>
                <TextEntryDiv>
                    <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" value={this.props.claim.claim_speaker} defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The person or organization that said or wrote the original claim."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_source' label="Claim Source" value={this.props.claim.claim_speaker} defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The source that published the original claim."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_date' label="Claim Date" value={this.props.claim.claim_date} InputProps={{readOnly: true}} variant="filled" tooltip="The date the original claim was made."/>
                    <SepSpaceDiv/>
                </TextEntryDiv>
                <TextEntryDiv >
                  <TextFieldWithTooltip name='claim_location' label="Location" value={location} defaultValue={location} InputProps={{readOnly: true}} variant="filled" tooltip="The location most relevant to the claim."/>
                    <SepSpaceDiv/>
                  {justification}
                </TextEntryDiv>
                <TextEntryDiv>
                  <div data-tour="verdict">
                    <SelectWithTooltip name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["label"]} label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip={
                  <ul>
                  <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                  <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                  <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                  <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
                  </ul>}
                    />
                    </div>
                  
                  <CheckboxBox data-tour="bias" control={<Checkbox  name="bias" checked={this.props.data["bias"]} onChange={this.handleCheckboxChange} />} label="I believe my verdict may be biased because of the answer sources." />
                    
                </TextEntryDiv>
                </ContainerDiv>
            </EntryCard>
        );
    }
}

export default ClaimTopField;