import React from 'react';
import QuestionGenerationBar from './QuestionGenerationBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import SearchField from '../components/SearchField';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import { WarningRounded } from '@material-ui/icons';

const QADataField = styled.div`
    width: -webkit-calc(40% - 10px)!important;
    width:    -moz-calc(40% - 10px)!important;
    width:         calc(40% - 10px)!important;
    float:left;
    overflow:auto;
    border-style:inset;
    border-width:2px;
    height: -webkit-calc(100% - 6px)!important;
    height:    -moz-calc(100% - 6px)!important;
    height:         calc(100% - 6px)!important;
`

const QAPageView = styled(ClaimPageView)`
    width:60%;
    float:left;
    border-style:inset;
    border-width:2px;
    height: -webkit-calc(100% - 6px)!important;
    height:    -moz-calc(100% - 6px)!important;
    height:         calc(100% - 6px)!important;
`

const QAPageDiv = styled.div`
    width: 100%;
    height: 100vh;
`

const WarningDiv = styled.div`
    color:#D0342C;
    display:inline;
    position:absolute;
    margin: -5px 0px 0px -2px;
`

class QuestionGeneration extends React.Component {
    constructor(props) {
        super(props);
        
        this.state = {
            claim : {
                web_archive: "https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/",
                claim_text: "New England Journal of Medicine finds that women who got v4x3d – within 30 days of becoming pregnant and up to 20 weeks pregnant – had a miscarriage rate of 82%",
                claim_speaker: "Ian Smith",
                claim_type: ["Numerical Claim"],
                fact_checking_strategy: ["Numerical Comparison", "Consultation"],
                claim_hyperlink: "https://archive.is/qpiqn",
                claim_date: "11/06/2021",
                country_code: "gb"
            },
            userIsFirstVisiting: true
        }
      }

    render() {
      var problemSourceText = <div>Searching the internet may return results from untrustworthy sources. We have compiled a list of the most common, and our search engine marks these with <WarningDiv><WarningRounded/></WarningDiv>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;. If possible, please avoid using these.</div>

        const steps = [
            {
                selector: '[data-tour="claim_text"]',
                content: "Begin by reading the claim."
            },
            {
              selector: '[data-tour="claim_page_view"]',
              content: "Carefully read the fact-checking article to see how this claim was verified."
            },
            {
              selector: '[data-tour="report"]',
              content: "If the fact-checking article shows a 404 page or another error, or if the article is behind a paywall, you can report it to us (although please give it a minute to load - some sites are not very fast)."
            },
            {
              selector: '[data-tour="question_textfield"]',
              content: "Based on the approach taken by the fact-checkers, formulate a question that will help you determine the truth of the claim."
            },
            {
              selector: '[data-tour="answer_textfield"]',
              content: "Find an answer to your question. You can use any sources linked to in the fact-checking article..."
            },
            {
              selector: '[data-tour="search"]',
              content: "... or find new sources using our custom search field."
            },
            {
              selector: '[data-tour="search"]',
              content: problemSourceText
            },
            {
              selector: '[data-tour="search"]',
              content: "The search engine may also return results from other fact-checking sites. If possible, please avoid using these as well."
            },
            {
              selector: '[data-tour="answer_metadata"]',
              content: "Please let us know on which page you found the answer, what kind of answer it is, and what kind of media (e.g. text, video) you found the answer in.",
            },
            {
              selector: '[data-tour="answer_type"]',
              content: "For some questions you might not be able to find an answer. That's fine - just leave the answer blank, select \"unanswerable\" as the type, and ask another question. It may be useful to ask a rephrased version of the question, or a version with more context, as the next question."
            },
            {
              selector: '[data-tour="add"]',
              content: "If one question is not enough to give a verdict for the claim (independent of the fact-checking article), you can add more questions. We expect you will need at least two questions for each claim, often more. Please ask all questions necessary to gather the evidence needed for the verdict, including world knowledge that might seem obvious."
            },
            {
              selector: '[data-tour="verdict"]',
              content: "Once you have collected enough question-answer pairs to give a verdict, select the most fitting option here (regardless of which verdict the fact-checking article gave). If you have not found enough information to verify or refute the claim after N minutes, please choose 'Not Enough Information' and proceed to the next claim."
            },
            {
              selector: '[data-tour="submit"]',
              content: "When you have verified the claim, submit your questions and your verdict and proceed to the next article."
            },
          ];

        return (
            <QAPageDiv>
                <TourProvider steps={steps}>
                <QAPageView claim={this.state.claim}/>
                <QADataField>
                    <QuestionGenerationBar claim={this.state.claim}/>
                    <SearchField claim_date={this.state.claim.claim_date} country_code={this.state.claim.country_code}/>
                </QADataField>
                {this.state.userIsFirstVisiting? <TourWrapper/> : ""}
                </TourProvider>
            </QAPageDiv>
        );
      }
}

export default QuestionGeneration;
