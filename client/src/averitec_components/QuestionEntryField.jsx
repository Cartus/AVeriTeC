import React from 'react';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import EntryCardContainer from '../components/EntryCardContainer';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import ClaimTopField from '../averitec_components/ClaimTopField';
import {notEmptyValidator} from '../utils/validation.js'
import Card from '@material-ui/core/Card';

const ContainerDiv = styled.div`
    width:100%;
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
  width:100%;
  margin: 20px 0px 0px 0px;
`

const QAGridElementRight = styled(Grid)`
    width: -webkit-calc(49% - 33px)!important;
    width:    -moz-calc(49% - 33px)!important;
    width:         calc(49% - 33px)!important;
`

const EmptySpaceDiv = styled.div`
    @media (min-width: 1675px)  {
        width:100%;
        margin: 5px 0px 0px 0px;
        margin: 5px 0px 0px 0px;
        margin: 5px 0px 0px 0px;
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

const QuestionReminderBox = styled.div`
  width: 280px;


  margin: 0px 0px 21px 0px;
  
  @media (max-width: 1674px)  {
    margin: 15px 0px 5px 0px;
  }
`

const EntryCard = styled(Card)`
  margin:10px;
  float:left;
  width: -webkit-calc(100% - 20px)!important;
  width:    -moz-calc(100% - 20px)!important;
  width:         calc(100% - 20px)!important;
`
/*
class AnswerCard extends React.Component{
  constructor(props){
    super(props);
  }

  render(){
    var unanswerable = this.props.data["answer_type"] == "Unanswerable"

    return <EntryCard>
      <TextLeftEntryDiv>
                    {unanswerable? 
                    <TextFieldWithTooltip data-tour="answer_textfield" disabled value={this.props.data["answer"]} name='answer' label="Answer" multiline rows={7} onChange={this.handleFieldChange} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
                    :
                    <TextFieldWithTooltip data-tour="answer_textfield" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["answer"]} name='answer' label="Answer" multiline rows={7} onChange={this.handleFieldChange} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
                  }
      </TextLeftEntryDiv>
      <TextRightEntryDiv>
                  <div data-tour="answer_metadata">
                  <div data-tour="answer_type">
                    <SelectWithTooltip name="answer_type" label="Answer Type" validator={notEmptyValidator} valid={this.props.valid} required  value={this.props.data["answer_type"]} onChange={this.handleAnswerTypeFieldChange} items={["Extractive", "Abstractive", "Boolean", "Unanswerable"]} tooltip={<ul>
                      <li>Extractive: The answer is a phrase copied directly from the source.</li>
                      <li>Abstractive: The answer was rephrased, but is based directly on the source.</li>
                      <li>Boolean: The answer is yes/no, based directly on the source.</li>
                      <li>Unanswerable: No source providing an answer to this question could be found.</li>
                      </ul>}/>
                    </div>
                    <EmptySpaceDiv/>
                    
                    {unanswerable? 
                    <TextFieldWithTooltip name='source_url' label="Source URL" disabled value={this.props.data["source_url"]} onChange={this.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here. Try to avoid using other fact-checking articles as sources."/> : 
                    <TextFieldWithTooltip name='source_url' label="Source URL" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["question"]} value={this.props.data["source_url"]} onChange={this.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here. Try to avoid using other fact-checking articles as sources."/>
                    }
                    
                    <EmptySpaceDiv/>
                    
                    {unanswerable? 
                    <SelectWithTooltip name="source_medium" label="Source Medium" disabled value={this.props.data["source_medium"]} onChange={this.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Please describe what medium you found the answer in."/>
                    :
                    <SelectWithTooltip name="source_medium" label="Source Medium" value={this.props.data["source_medium"]} onChange={this.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Please describe what medium you found the answer in."/>
                    }
                    </div>
      </TextRightEntryDiv>
    </EntryCard>
  }
}*/

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

    handleAnswerTypeFieldChange = event => {
      const { name, value } = event.target;

      if (value == "Unanswerable"){
        this.props.onChange(this.props.id, "source_url", "");
        this.props.onChange(this.props.id, "source_medium", "");
        this.props.onChange(this.props.id, "answer", "No answer could be found.");
      }

      this.props.onChange(this.props.id, name, value);
    }    

    handleDelete = () => {
      this.props.onDelete(this.props.id)
    }

    render() {
      var unanswerable = this.props.data["answer_type"] == "Unanswerable"


        return (
            <ContainerDiv>
                <TextLeftEntryDiv>
                    <TextFieldWithTooltip data-tour="question_textfield" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["question"]} name='question' label="Question" required multiline rows={7} onChange={this.handleFieldChange} tooltip="Please write a question that will help you gather evidence for or against the claim."/>
                    <PaddingDiv/>
                    {unanswerable? 
                    <TextFieldWithTooltip data-tour="answer_textfield" disabled value={this.props.data["answer"]} name='answer' label="Answer" multiline rows={7} onChange={this.handleFieldChange} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
                    :
                    <TextFieldWithTooltip data-tour="answer_textfield" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["answer"]} name='answer' label="Answer" multiline rows={7} onChange={this.handleFieldChange} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
                  }
                </TextLeftEntryDiv>
                
                <TextRightEntryDiv>
                  <QuestionReminderBox>
                    When entering a question, please ensure that it:
                    <ul>
                      <li>Is a well-formed sentence, rather than a search-engine query.</li>
                      <li>Does not refer to named entities that appear only in the fact-checking article, and not in the claim, in metadata, or in previous answers.</li>
                      <li>Does not directly ask whether the claim holds, e.g. 'is it true that [claim]'.</li>
                    </ul>
                  </QuestionReminderBox>
                  <div data-tour="answer_metadata">
                  <div data-tour="answer_type">
                    <SelectWithTooltip name="answer_type" label="Answer Type" validator={notEmptyValidator} valid={this.props.valid} required  value={this.props.data["answer_type"]} onChange={this.handleAnswerTypeFieldChange} items={["Extractive", "Abstractive", "Boolean", "Unanswerable"]} tooltip={<ul>
                      <li>Extractive: The answer is a phrase copied directly from the source.</li>
                      <li>Abstractive: The answer was rephrased, but is based directly on the source.</li>
                      <li>Boolean: The answer is yes/no, based directly on the source.</li>
                      <li>Unanswerable: No source providing an answer to this question could be found.</li>
                      </ul>}/>
                    </div>
                    <EmptySpaceDiv/>
                    
                    {unanswerable? 
                    <TextFieldWithTooltip name='source_url' label="Source URL" disabled value={this.props.data["source_url"]} onChange={this.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here. Try to avoid using other fact-checking articles as sources."/> : 
                    <TextFieldWithTooltip name='source_url' label="Source URL" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["question"]} value={this.props.data["source_url"]} onChange={this.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here. Try to avoid using other fact-checking articles as sources."/>
                    }
                    
                    <EmptySpaceDiv/>
                    
                    {unanswerable? 
                    <SelectWithTooltip name="source_medium" label="Source Medium" disabled value={this.props.data["source_medium"]} onChange={this.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Please describe what medium you found the answer in."/>
                    :
                    <SelectWithTooltip name="source_medium" label="Source Medium" value={this.props.data["source_medium"]} onChange={this.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Please describe what medium you found the answer in."/>
                    }
                    </div>
                </TextRightEntryDiv>
            </ContainerDiv>
        );
      }
}

export default QuestionEntryField;