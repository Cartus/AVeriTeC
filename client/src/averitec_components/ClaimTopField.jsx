import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import {notEmptyValidator} from '../utils/validation.js'
import countryFlagEmoji from "country-flag-emoji";
import Checkbox from '@mui/material/Checkbox';
import { FormControlLabel } from '@mui/material';

const EntryCard = styled(Card)`
  margin:10px;
`

const CheckboxBox = styled(FormControlLabel)`
  width: 300px;
  margin: 20px 5px;
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

const TextBottomEntryDiv = styled.div`
  float:left;

  @media  (max-width: 1674px)  {
    margin: 0px 0px 0px -webkit-calc(50% - 140px)!important;
    margin: 0px 0px 0px    -moz-calc(50% - 140px)!important;
    margin: 0px 0px 0px         calc(50% - 140px)!important;
  }

  @media (min-width: 1675px)  {
    margin: 0px 0px 20px -webkit-calc((100% - 586px)/3)!important;
    margin: 0px 0px 20px    -moz-calc((100% - 586px)/3)!important;
    margin: 0px 0px 20px         calc((100% - 586px)/3)!important;
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
        this.handleCheckboxChange = this.handleCheckboxChange.bind(this);
    }

    handleFieldChange = event => {
        const { name, value } = event.target;
        this.props.onChange(this.props.id, name, value);
    }

    handleDelete = () => {
      this.props.onDelete(this.props.id)
    }

    handleCheckboxChange = event => {
      this.props.onChange(this.props.id, event.target.name, event.target.checked);

      if (!event.target.checked){
        this.props.onChange(this.props.id, "claim_correction", "");
      }
  }

    render() {
      var location = ""
      if (this.props.claim && this.props.claim.country_code){
        var country_list = countryFlagEmoji.list.filter((entry) => {return !["European Union", "United Nations"].includes(entry.name)}).sort( (a,b) => {
          var textA = a.name.toUpperCase();
          var textB = b.name.toUpperCase();
          return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
        });
      
        var country_by_code_dict = country_list.reduce((a,x) => ({...a, [x.code]: x}), {})
        location = country_by_code_dict[this.props.claim.country_code].name + " (" + country_by_code_dict[this.props.claim.country_code].code + ")"
      }
      

        return (
            <EntryCard>
                <ContainerDiv>

                <ClaimHeader data-tour="claim_text">{this.props.claim.claim_text}</ClaimHeader>
                <TextLeftEntryDiv>
                    <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" value={this.props.claim.claim_speaker} defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The person or organization that said or wrote the original claim."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_source' label="Claim Source" value={this.props.claim.claim_source} defaultValue={this.props.claim.claim_source} InputProps={{readOnly: true}} variant="filled" tooltip="The source that published the original claim."/>
                    <SepSpaceDiv/>
                    <EmptySpaceDiv/>
                    <TextFieldWithTooltip name='claim_date' label="Claim Date" value={this.props.claim.claim_date} defaultValue={this.props.claim.claim_date} InputProps={{readOnly: true}} variant="filled" tooltip="The date the original claim was made."/>
                    <SepSpaceDiv/>
                    <TextFieldWithTooltip name='claim_location' label="Location" value={location} defaultValue={location} InputProps={{readOnly: true}} variant="filled" tooltip="The location most relevant to the claim."/>
                </TextLeftEntryDiv>
                <TextRightEntryDiv>
                <CheckboxBox data-tour="should_correct" control={<Checkbox  name="should_correct" checked={this.props.data["should_correct"]? this.props.data["should_correct"] : false} onChange={this.props.posthocView ? () => { } : this.handleCheckboxChange} />} label="I think the claim has been formatted wrong. It should be:" />
                {
                  (this.props.data["should_correct"] && !this.props.posthocView)?
                  <TextFieldWithTooltip name='claim_correction' label="Correction" multiline rows={3} value={this.props.data["claim_correction"]} validator={notEmptyValidator} valid={this.props.valid} onChange={this.handleFieldChange} required tooltip="A correction for the claim text, if you think it is necessary"/>
                  :
                  <TextFieldWithTooltip name='claim_correction' label="Correction" multiline rows={3} value={this.props.data["claim_correction"]} InputProps={{readOnly: true}} variant="filled" tooltip="A correction for the claim text, if you think it is necessary."/>
                }
                </TextRightEntryDiv>                   
                </ContainerDiv>
            </EntryCard>
        );
    }
}

export default ClaimTopField;