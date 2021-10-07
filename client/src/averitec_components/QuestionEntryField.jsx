import React from 'react';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import EntryCardContainer from '../components/EntryCardContainer';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import ClaimTopField from '../averitec_components/ClaimTopField';
import {notEmptyValidator, atLeastOneValidator} from '../utils/validation.js'

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
    width: -webkit-calc(49% - 30px)!important;
    width:    -moz-calc(49% - 30px)!important;
    width:         calc(49% - 30px)!important;
`

const PaddingDiv = styled.div`
    width:63px;
`

const QAGridElementRight = styled(Grid)`
    width: -webkit-calc(49% - 33px)!important;
    width:    -moz-calc(49% - 33px)!important;
    width:         calc(49% - 33px)!important;
`

class QuestionEntryField extends React.Component {
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
                        <TextFieldWithTooltip validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["question"]} name='question' label="Question" required multiline rows={2} onChange={this.handleFieldChange} tooltip="Please write a question that will help you gather evidence for or against the claim."/>
                    </ColumnDiv>
                </QAGridElementLeft>
                <PaddingDiv/>
                <Divider orientation="vertical" flexItem />
                <QAGridElementRight item xs>
                    <ColumnDiv>
                        <TextFieldWithTooltip validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["answer"]} name='answer' label="Answer" required multiline rows={2} onChange={this.handleFieldChange} tooltip="Please write the answer here."/>
                        <TextFieldWithTooltip name='answer_url' label="URL" onChange={this.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here."/>
                        <SelectWithTooltip name="answer_type" label="Answer Type" onChange={this.handleFieldChange} items={["Extractive", "Abstractive", "Boolean", "Unanswerable"]} tooltip="Helpful tooltip TBA"/>
                        <SelectWithTooltip name="answer_medium" label="Answer Medium" onChange={this.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Describe what medium you found the answer in."/>
                    </ColumnDiv>
                </QAGridElementRight>
            </QAGrid>
            </div>
        );
      }
}

export default QuestionEntryField