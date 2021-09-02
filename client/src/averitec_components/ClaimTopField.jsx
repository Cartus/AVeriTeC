import React from 'react';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';

const EntryCard = styled(Card)`
  margin:10px;
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
        if (this.props.ask_for_justification){
            var justification = <TextFieldWithTooltip name='justification' label="Justification" required multiline rows={2} onChange={this.handleFieldChange} tooltip="Please write a short explanation (max 100 words) for how you decided the answer based on the questions."/>
        } else{
            var justification = ""
        }


        return (
            <EntryCard>
                <h4>{this.props.claim.claim_text}</h4>
                <TextFieldWithTooltip name='claim_speaker' label="Claim Speaker" defaultValue={this.props.claim.claim_speaker} InputProps={{readOnly: true}} variant="filled" tooltip="The name of the person or organization who produced the claim"/>
                <TextFieldWithTooltip name='claim_date' label="Claim Date" defaultValue={this.props.claim.claim_date} InputProps={{readOnly: true}} variant="filled" tooltip="The date the claim was made"/>
                
                {this.props.claim.fact_checking_strategy}
                {this.props.claim.claim_type}
                <SelectWithTooltip name="label" label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip="
                <ul>
                <li>Supported: The claim is fully supported by the arguments and evidence presented.
                <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.
                <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.
                <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found. Missing context may also be relevant if a situation has changed over time, and the claim fails to mention this.</ul>"
                />

                {justification}
            </EntryCard>
        );
    }
}

export default ClaimTopField;