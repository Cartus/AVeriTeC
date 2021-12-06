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
    width: -webkit-calc(25%)!important;
    width:    -moz-calc(25%)!important;
    width:         calc(25%)!important;
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
    height:10px;
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
        this.handleAnswerFieldChange = this.handleAnswerFieldChange.bind(this);
    }

    handleFieldChange = event => {
        const { name, value } = event.target;
        this.props.onChange(this.props.id, name, value);
    }

    handleAnswerFieldChange = (index, event) => {
        const { name, value } = event.target;
        this.props.data["answers"][index][name] = value
        
        this.props.onChange(this.props.id, "answers", this.props.answers);
    }

    render() {
        if (!this.props.data["answers"]){
            this.props.data["answers"] = [{},]
        }

        const answerFields = this.props.data["answers"].map((answer, index) => (
            <div>
            <ColumnDiv>
                <SourceSpacingDiv2/>
                <LargeTextField name='answer' label="Answer" InputProps={{readOnly: true}} variant="filled" value={answer.answer} multiline rows={3}/>
                {answer.url? 
                <div>
                    <SourceSpacingDiv/>
                    <span><a href={answer.url}>View source</a></span>
                </div>
                :
                ""
                }
            </ColumnDiv>
            <ColumnDiv>
                <SourceSpacingDiv/>
                <AtLeastOneCheckboxGroup 
                    name="answer_problems" 
                    label="Potential problems with this answer. Check any you think apply:" 
                    data={answer["answer_problems"]}
                    items={[
                        {label: "The answer is not understandable/readable", tooltip: "Please check this box if the answer is empty, gibberish, or ungrammatical to the point where you cannot understand it."},
                        {label: "Answer seems wrong, but is supported by the source", tooltip: "Please check this box if you believe the answer might be wrong, but the source supports the answer."},
                        {label: "Answer seems wrong, and is not supported by the source", tooltip: "Please check this box if you believe the answer might be wrong, and you discover that it contradicts the source."},
                        {label: "I believe the source may be biased", tooltip: "Please check this box if you believe the source is a heavily biased website."},
                    ]} 
                    onChange={(event) => this.handleAnswerFieldChange(index, event)}
                    tooltip="If you believe there are problems with this answer, please tick the appropriate boxes here. If you identify problems with an answer, please do not use it to support your verdict."
                />
            </ColumnDiv>
            </div>
          ));

        return (
            <div>
              <QAGrid container direction="row" justifyContent="space-evenly" alignItems="center" spacing={3}>
                <QAGridElementLeft item xs>
                    <ColumnDiv>
                        <ExtraLeftSpacing/>
                        <LargeTextField name='question' label="Question" InputProps={{readOnly: true}} variant="filled"  value={this.props.question.text} multiline rows={3}/>
                    </ColumnDiv>
                    <ColumnDiv>
                        <ExtraLeftSpacing/>
                        <AtLeastOneCheckboxGroup 
                        name="question_problems" 
                        label="Potential problems with this question. Check any you think apply:" 
                        data={this.props.question["question_problems"]}
                        items={[
                            {label: "The question is not understandable/readable", tooltip: "Please check this box if the question is empty, gibberish, or ungrammatical to the point where you cannot understand it."},
                            {label: "The question is unrelated to the claim", tooltip: "Please check this box if the question does not seem relevant to verifying the claim."},
                        ]} 
                        onChange={this.handleFieldChange}
                        tooltip="If you believe there are problems with this question, please tick the appropriate boxes here. If you identify problems with a question, please do not use it to support your verdict."
                        />
                    </ColumnDiv>
                </QAGridElementLeft>
                <Divider orientation="vertical" flexItem />
                <Divider orientation="vertical" flexItem />
                <QAGridElementRight item xs>
                    {answerFields}
                </QAGridElementRight>
            </QAGrid>
            </div>
        );
      }
}

export default StaticQuestionEntryField