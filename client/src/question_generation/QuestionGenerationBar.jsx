
import React from 'react';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import EntryCardContainer from '../components/EntryCardContainer';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import Card from '@material-ui/core/Card';

const EntryCard = styled(Card)`
  margin:10px;
`

const ColumnDiv = styled.div`
    width:100%;
    margin: 20px;
`

const QAGrid = styled(Grid)`
    float: left;
    width: -webkit-calc(100% - 16px)!important;
    width:    -moz-calc(100% - 16px)!important;
    width:         calc(100% - 16px)!important;
`

const QAGridElementLeft = styled(Grid)`
    width: -webkit-calc(51%)!important;
    width:    -moz-calc(51%)!important;
    width:         calc(51%)!important;
`

const PaddingDiv = styled.div`
    width:63px;
`

const QAGridElementRight = styled(Grid)`
    width: -webkit-calc(49% - 63px)!important;
    width:    -moz-calc(49% - 63px)!important;
    width:         calc(49% - 63px)!important;
`

class QATopField extends React.Component {
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
                <SelectWithTooltip name="phase_2_label" label="Claim Label" onChange={this.handleFieldChange} items={["Supported", "Refuted", "Not Enough Information", "Missing Context"]} tooltip="
                <ul>
                <li>Supported: The claim is fully supported by the arguments and evidence presented.
                <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.
                <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.
                <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found. Missing context may also be relevant if a situation has changed over time, and the claim fails to mention this.</ul>"
                />
            </EntryCard>
        );
    }
}

class QAEntryField extends React.Component {
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
            <div>
              <QAGrid container direction="row" justifyContent="space-evenly" alignItems="center" spacing={3}>
                <QAGridElementLeft item xs>
                    <ColumnDiv>
                        <TextFieldWithTooltip name='question' label="Question" required multiline rows={2} onChange={this.handleFieldChange} tooltip="Please write a question that will help you gather evidence for or against the claim."/>
                    </ColumnDiv>
                </QAGridElementLeft>
                <PaddingDiv/>
                <Divider orientation="vertical" flexItem />
                <QAGridElementRight item xs>
                    <ColumnDiv>
                        <TextFieldWithTooltip name='answer' label="Answer" required multiline rows={2} onChange={this.handleFieldChange} tooltip="Please write the answer here."/>
                        <TextFieldWithTooltip name='answer_url' label="URL" onChange={this.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here."/>
                        <SelectWithTooltip name="answer_type" label="Answer Type" onChange={this.handleFieldChange} items={["Extractive", "Abstractive", "Boolean", "Unanswerable"]} tooltip="Helpful tooltip TBA"/>
                    </ColumnDiv>
                </QAGridElementRight>
            </QAGrid>
            </div>
        );
      }
}

class QuestionGenerationBar extends React.Component {
    constructor(props) {
        super(props);
      }

    render() {
        return (
            <EntryCardContainer 
            headerClass={QATopField}
            contentClass={QAEntryField} 
            entryName="qa_pair" 
            addTooltip="Add another question."
            numInitialEntries={2}
            />
        );
      }
}

export default QuestionGenerationBar