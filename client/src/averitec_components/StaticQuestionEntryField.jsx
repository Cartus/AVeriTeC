import React from 'react';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import TextField from '@material-ui/core/TextField';
import AtLeastOneCheckboxGroup from '../components/AtLeastOneCheckboxGroup';

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

const ExtraLeftSpacing = styled.div`
    padding:10px;
    float:left;
`

const SourceSpacingDiv2 = styled.div`
    width:100%;
    height:22px;
`

const SourceSpacingDiv = styled.div`
    width:100%;
    height:5px;
`

const QAGridElementRight = styled(Grid)`
    width: -webkit-calc(49% - 63px)!important;
    width:    -moz-calc(49% - 63px)!important;
    width:         calc(49% - 63px)!important;
`

const LargeTextField = styled(TextField)`
width: -webkit-calc(100% - 80px)!important;
width:    -moz-calc(100% - 80px)!important;
width:         calc(100% - 80px)!important;
`

class StaticQuestionEntryField extends React.Component {
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
            <div>
              <QAGrid container direction="row" justifyContent="space-evenly" alignItems="center" spacing={3}>
                <QAGridElementLeft item xs>
                    <ColumnDiv>
                        <ExtraLeftSpacing/>
                        <LargeTextField name='question' label="Question" InputProps={{readOnly: true}} variant="filled"  value={this.props.question.text} multiline rows={3}/>
                    </ColumnDiv>
                </QAGridElementLeft>
                <Divider orientation="vertical" flexItem />
                <QAGridElementLeft item xs>
                    <ColumnDiv>
                        <SourceSpacingDiv2/>
                        <LargeTextField name='answer' label="Answer" InputProps={{readOnly: true}} variant="filled" value={this.props.question.answer} multiline rows={3}/>
                        <SourceSpacingDiv/>
                        <span>Source: <a href={this.props.question.url}>{this.props.question.url}</a></span>
                    </ColumnDiv>
                </QAGridElementLeft>
                <Divider orientation="vertical" flexItem />
                <QAGridElementRight item xs>
                    <ColumnDiv data-tour="report_qa_problems">
                        <AtLeastOneCheckboxGroup 
                        name="question_problems" 
                        label="Potential problems with the question-answer pair. Check any you think apply:" 
                        data={this.props.question["question_problems"]}
                        items={[
                            {label: "The question is not understandable/readable", tooltip: "Please check this box if the question is empty, gibberish, or ungrammatical to the point where you cannot understand it."},
                            {label: "The answer is not understandable/readable", tooltip: "Please check this box if the answer is empty, gibberish, or ungrammatical to the point where you cannot understand it."},
                            {label: "Answer seems wrong, but is supported by the source", tooltip: "Please check this box if you believe the answer might be wrong, but the source supports the answer."},
                            {label: "Answer seems wrong, and is not supported by the source", tooltip: "Please check this box if you believe the answer might be wrong, and you discover that it contradicts the source."},
                        ]} 
                        onChange={this.handleFieldChange}
                        tooltip="If you believe there are problems with this question, please tick the appropriate boxes here. If you identify problems with a question, please do not use it to support your verdict."
                        />
                    </ColumnDiv>
                </QAGridElementRight>
            </QAGrid>
            </div>
        );
      }
}

export default StaticQuestionEntryField