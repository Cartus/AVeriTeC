import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import Button from '@material-ui/core/Button';
import SelectWithTooltip from '../components/SelectWithTooltip';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'

const EntryCard = styled(Card)`
  margin:10px;
`

const VerdictDiv = styled.div`
  float:left;

  margin: 20px 0px 20px -webkit-calc(50% - 140px)!important;
  margin: 20px 0px 20px    -moz-calc(50% - 140px)!important;
  margin: 20px 0px 20px         calc(50% - 140px)!important;
`

class VerdictBar extends React.Component {
    constructor(props) {
        super(props);
        
        this.handleFieldChange = this.handleFieldChange.bind(this);
    }

    handleFieldChange = event => {
      const { name, value } = event.target;
      this.props.onChange(this.props.id, name, value);
  }

    render() {
        return (
            <EntryCard>
              <VerdictDiv data-tour="verdict">
                    <SelectWithTooltip readOnly={this.props.posthocView} name="label" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data? this.props.data["label"] : ""} label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Evidence", "Conflicting Evidence/Cherrypicking"]} tooltip={
                  <ul>
                  <li>Supported: The claim is fully supported by the arguments and evidence presented.</li>
                  <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.</li>
                  <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.</li>
                  <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found.</li>
                  </ul>}
                    />
              </VerdictDiv>
            </EntryCard>
        );
    }
}

export default VerdictBar;