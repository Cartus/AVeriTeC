import React from 'react';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import EntryCardContainer from '../components/EntryCardContainer';
import TextFieldWithTooltip from '../components/TextFieldWithTooltip';
import SelectWithTooltip from '../components/SelectWithTooltip';
import ClaimTopField from '../averitec_components/ClaimTopField';
import {notEmptyValidator} from '../utils/validation.js'
import Slider from '@mui/material/Slider';
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


const marks = [
  {
    value: 1,
    label: 'One answer',
  },
  {
    value: 2,
    label: 'Two answers',
  },
  {
    value: 3,
    label: 'Three answers',
  },
];

function valuetext(value) {
  return `${value}`;
}

function valueLabelFormat(value) {
  return marks.findIndex((mark) => mark.value === value) + 1;
}

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

const MidDiv = styled.div`
  float:left;
  margin: 20px 0px 20px 20px;
`

const QuestionReminderBox = styled.div`
  width: 280px;


  margin: 0px 0px 21px 0px;
  
  @media (max-width: 1674px)  {
    margin: 15px 0px 5px 0px;
  }
`

const MidSlider = styled(Slider)`
  width:70%!important;
  margin:15px 15% 0px 15%!important;
`

const EntryCard = styled(Card)`
  margin:10px;
  float:left;
  width: -webkit-calc(100% - 20px)!important;
  width:    -moz-calc(100% - 20px)!important;
  width:         calc(100% - 20px)!important;
`

class AnswerCard extends React.Component{
  constructor(props){
    super(props);
  }

  render(){
    var unanswerable = this.props.data["answer_type"] == "Unanswerable"
      var boolean = this.props.data["answer_type"] == "Boolean"

      var answer_field = <div>
                        <TextFieldWithTooltip data-tour="answer_textfield" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["answer"]} name='answer' label="Answer" multiline rows={7} onChange={this.props.handleFieldChange} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
                        <PaddingDiv/>
                        </div>
      if (unanswerable){
        answer_field = <div>
          <TextFieldWithTooltip data-tour="answer_textfield" disabled value={this.props.data["answer"]} name='answer' label="Answer" multiline rows={7} onChange={this.props.handleFieldChange} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
          <PaddingDiv/>
          </div>
          }

      if (boolean){
        answer_field = <div data-tour="answer_textfield">
          <SelectWithTooltip name="answer" label="Answer" validator={notEmptyValidator} valid={this.props.valid} required  value={this.props.data["answer"]} onChange={this.props.handleFieldChange} items={["Yes", "No"]} tooltip="Please write the answer here. Use the links in the fact checking article, or any article you find using our search engine below, to support your answer with evidence."/>
          <TextFieldWithTooltip name='bool_explanation' label="Explanation" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["bool_explanation"]} multiline rows={5} onChange={this.props.handleFieldChange} tooltip="Please write a short explanation for your yes/no answer here."/> 
                    
        </div>
      }

    return <EntryCard>
              <TextLeftEntryDiv>
                    {answer_field}
                </TextLeftEntryDiv>
                
                <TextRightEntryDiv>
                  <div data-tour="answer_metadata">
                  <div data-tour="answer_type">
                    <SelectWithTooltip name="answer_type" label="Answer Type" validator={notEmptyValidator} valid={this.props.valid} required  value={this.props.data["answer_type"]} onChange={this.props.handleAnswerTypeFieldChange} items={["Extractive", "Abstractive", "Boolean", "Unanswerable"]} tooltip={<ul>
                      <li>Extractive: The answer is a phrase copied directly from the source.</li>
                      <li>Abstractive: The answer was rephrased, but is based directly on the source.</li>
                      <li>Boolean: The answer is yes/no, based directly on the source.</li>
                      <li>Unanswerable: No source providing an answer to this question could be found.</li>
                      </ul>}/>
                    </div>
                    <EmptySpaceDiv/>
                    
                    {unanswerable? 
                    <TextFieldWithTooltip name='source_url' label="Source URL" disabled value={this.props.data["source_url"]} onChange={this.props.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here. Try to avoid using other fact-checking articles as sources."/> : 
                    <TextFieldWithTooltip name='source_url' label="Source URL" validator={notEmptyValidator} valid={this.props.valid} required value={this.props.data["question"]} value={this.props.data["source_url"]} onChange={this.props.handleFieldChange} tooltip="Please copy-paste the URL where you found the answer here. Try to avoid using other fact-checking articles as sources."/>
                    }
                    
                    <EmptySpaceDiv/>
                    
                    {unanswerable? 
                    <SelectWithTooltip name="source_medium" label="Source Medium" disabled value={this.props.data["source_medium"]} onChange={this.props.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Please describe what medium you found the answer in."/>
                    :
                    <SelectWithTooltip name="source_medium" label="Source Medium" value={this.props.data["source_medium"]} onChange={this.props.handleFieldChange} items={["Web text", "Web table", "PDF", "Image/graphic", "Video", "Audio", "Other"]} tooltip="Please describe what medium you found the answer in."/>
                    }
                    </div>
                </TextRightEntryDiv>
    </EntryCard>
  }
}

class QuestionEntryField extends React.Component {
    constructor(props) {
        super(props);

        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.handleDelete = this.handleDelete.bind(this)
        this.handleAnswerFieldChange = this.handleAnswerFieldChange.bind(this);
        this.handleAnswerCountChange = this.handleAnswerCountChange.bind(this);
        this.handleAnswerTypeFieldChange = this.handleAnswerTypeFieldChange.bind(this);
    }

    handleFieldChange = event => {
        const { name, value } = event.target;
        console.log("set " + this.props.id + "." + name + " to " + value)
        this.props.onChange(this.props.id, name, value);
    }

    handleAnswerFieldChange = (index, event) => {
      const { name, value } = event.target;
      this.props.data["answers"][index][name] = value
      
      console.log("set " + index + "." + name + " to " + value)
      console.log(this.props.data["answers"])

      this.props.onChange(this.props.id, "answers", this.props.data["answers"]);
    }

    handleAnswerCountChange = event => {
      const { name, value } = event.target;

      while (value > this.props.data["answers"].length){
        this.props.data["answers"].push({})
      }

      while (value < this.props.data["answers"].length){
        this.props.data["answers"].pop()
      }

      this.props.onChange(this.props.id, "answers", this.props.data["answers"]);
  }

    handleAnswerTypeFieldChange = (index, event) => {
      const { name, value } = event.target;

      if (value == "Unanswerable"){
        this.props.data["answers"][index]["source_url"] = ""
        this.props.data["answers"][index]["source_medium"] = ""
        this.props.data["answers"][index]["answer"] = "No answer could be found."
      }

      if (value == "Boolean"){
        if (this.props.data["answer"] != "Yes" && this.props.data["answer"] != "No"){
          this.props.data["answers"][index]["answer"] = ""
        }
      } else if (this.props.data["answer_type"] == "Boolean"){
        this.props.data["answers"][index]["bool_explanation"] = ""
      }

      this.props.data["answers"][index][name] = value
      this.props.onChange(this.props.id, "answers", this.props.data["answers"]);
    }    

    handleDelete = () => {
      this.props.onDelete(this.props.id)
    }

    render() {
      if (!this.props.data["answers"]){
        this.props.data["answers"] = [{},]
      }
      const answerFields = this.props.data["answers"].map((answer, index) => (
        <AnswerCard 
        data={answer} 
        valid={this.props.valid} 
        handleFieldChange={(event) => this.handleAnswerFieldChange(index, event)} 
        handleAnswerTypeFieldChange={(event) => this.handleAnswerTypeFieldChange(index, event)}/>
      ));
      

        return (
            <ContainerDiv>
                <TextLeftEntryDiv>
                    <TextFieldWithTooltip data-tour="question_textfield" validator={notEmptyValidator} valid={this.props.valid} maxCharacters={500} required value={this.props.data["question"]} name='question' label="Question" required multiline rows={7} onChange={this.handleFieldChange} tooltip="Please write a question that will help you gather evidence for or against the claim."/>
                    <PaddingDiv/>
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
                </TextRightEntryDiv>

                <MidDiv data-tour="add_answers">
                <div>
                  If you find multiple answers to your question, you can add additional answers here. Please try to rephrase the question to yield a single answer BEFORE you add additional answers.
                </div>
                <MidSlider
                aria-label="Answers"
                name="n_answers"
                value={this.props.data["answers"]? this.props.data["answers"].length : 1}
                getAriaValueText={valuetext}
                step={null}
                min={1}
                max={3}
                onChange={this.handleAnswerCountChange}
                valueLabelDisplay="auto"
                marks={marks}
                />
                </MidDiv>

                {answerFields}

                <AnswerCard data={this.props.data} valid={this.props.valid} handleFieldChange={this.handleFieldChange} handleAnswerTypeFieldChange={this.handleAnswerTypeFieldChange}/>
            </ContainerDiv>
        );
      }
}

export default QuestionEntryField;