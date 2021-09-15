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

const PaddingDiv = styled.div`
    width:63px;
`

const QAGridElementRight = styled(Grid)`
    width: -webkit-calc(49% - 63px)!important;
    width:    -moz-calc(49% - 63px)!important;
    width:         calc(49% - 63px)!important;
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
                        <TextField name='question' label="Question" InputProps={{readOnly: true}} variant="filled"  value={this.props.question.text} multiline rows={2}/>
                        <TextField name='answer' label="Answer" InputProps={{readOnly: true}} variant="filled" value={this.props.question.answer} multiline rows={2}/>
                        <span>Source: <a href={this.props.question.url}>{this.props.question.url}</a></span>
                    </ColumnDiv>
                </QAGridElementLeft>
                <PaddingDiv/>
                <Divider orientation="vertical" flexItem />
                <QAGridElementRight item xs>
                    <ColumnDiv>
                        <span>Check any you think apply:</span>
                        <AtLeastOneCheckboxGroup 
                        name="question_problems" 
                        label="Question validation" 
                        data={this.props.question["question_problems"]}
                        items={[
                            {label: "Unreadable question", tooltip: "tt"},
                            {label: "Unreadable answer", tooltip: "tt"},
                            {label: "Answer seems wrong, but is supported by the source", tooltip: "tt"},
                            {label: "Answer seems wrong, and is not supported by the source", tooltip: "tt"},
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