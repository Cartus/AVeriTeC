import React from 'react';
import QuestionGenerationBar from './QuestionGenerationBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import SearchField from '../components/SearchField';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import { WarningRounded } from '@material-ui/icons';
import axios from "axios";
import {Redirect} from "react-router-dom";
import config from "../config.json"
import moment from "moment";

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
                web_archive: "",
                claim_text: "",
                claim_speaker: "",
                claim_date: "",
		        country_code: ""
            },
            entries : {},
            qa_pair_header: {},
            qa_pair_footer: {},
            userIsFirstVisiting: false
        }
    }

    componentDidMount() {
        if (localStorage.getItem('login')) {
            let pc = Number(localStorage.pc);
            if (pc !== 0) {
		        var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/question_answering.php",
                    data:{
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'reload-data',
                        offset: pc - 1
                    }
                };

                axios(request).then((response) => {
                    if (response.data) {
                        console.log(response.data);
                        const new_claim = {
                            web_archive: response.data.web_archive,
                            claim_text: response.data.cleaned_claim,
                            claim_speaker: response.data.speaker,
                            claim_date: response.data.check_date,
			                country_code: response.data.country_code,
                            claim_source: response.data.claim_source
                        };
                        
                        if (new_claim.claim_date){
                            var claim_date = new Date(new_claim.claim_date + "T00:00:00.0Z");
                            new_claim.claim_date = moment(claim_date).format('DD/MM/YYYY');
                        }
                        
                        localStorage.claim_norm_id = response.data.claim_norm_id;
                        this.setState({claim: new_claim});

                        const new_header = {claim_correction: response.data.claim_correction, should_correct:response.data.should_correct};
                        this.setState({qa_pair_header: new_header})
                        console.log(this.state.qa_pair_header);

                        const new_footer = {label: response.data.label};
                        this.setState({qa_pair_footer: new_footer})


                        const new_entries = response.data.entries;
                        this.setState({entries: new_entries});
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {window.alert(error)})	    
            } else {
		        var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/question_answering.php",
                    data:{
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'next-data'
                    }
                };

                axios(request).then((response) => {
                    if (response.data) {
                        if (Number(localStorage.finished_qa_annotations) === 0) {
                            this.setState({userIsFirstVisiting: true});
                        }
                        console.log(response.data);
                        const new_claim = {
                            web_archive: response.data.web_archive,
                            claim_text: response.data.cleaned_claim,
                            claim_speaker: response.data.speaker,
                            claim_date: response.data.check_date,
			                country_code: response.data.country_code,
                            claim_source: response.data.claim_source
                        };

                        if (new_claim.claim_date){
                            var claim_date = new Date(new_claim.claim_date + "T00:00:00.0Z");
                            new_claim.claim_date = moment(claim_date).format('DD/MM/YYYY');
                        }

                        localStorage.claim_norm_id = response.data.claim_norm_id;
                        this.setState({claim: new_claim});
                        const new_entries = {"qa_pair_entry_field_0":{}};
                        this.setState({entries: new_entries});
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {window.alert(error)})    
            }
        }
    }

    render() {
        if (!localStorage.getItem('login')) {
            return <Redirect to='/'/>;
        }

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
              content: "Based on the approach taken by the fact-checkers, formulate a question that will help you determine the truth of the claim. This should be a real question rather than a search engine query, e.g. \"Who is the prime minister of Britain?\" rather than \"prime minister Britain\"."
            },
            {
              selector: '[data-tour="answer_textfield"]',
              content: "Find an answer to your question. You can use any sources linked to in the fact-checking article..."
            },
            {
              selector: '[data-tour="search"]',
              content: "... or if those do not give you the answers you need, find new sources using our custom search field."
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
              selector: '[data-tour="search"]',
              content: "Similarly, if possible please avoid using results directly originating from the source or the speaker of the claim."
            },
            {
              selector: '[data-tour="claim_page_view"]',
              content: "WARNING: For persistence, we have stored all fact-checking articles on archive.org Fact-checking articles may feature \"double-archived\" links using both archive.org and archive.is, e.g. \"https://web.archive.org/web/20201229212702/https://archive.md/28fMd\". Archive.org returns a 404 page for these. To view such a link, please just copy-paste the archive.is part (e.g. \"https://archive.md/28fMd\") into your browser."
            },
            {
              selector: '[data-tour="claim_page_view"]',
              content: "Links to archive.org allow you to select an archival date. Try to rely only on evidence that has appeared on the internet before the claim; e.g. with archive.org, if possible choose a date from before the claim was made."
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
              selector: '[data-tour="answer_type"]',
              content: "When possible, we prefer extractive answers."
            },
            {
              selector: '[data-tour="add_answers"]',
              content: "If you find multiple different answers to a question (e.g. because you find disagreeing sources), you can write in additional answers. Please only add additional answers if you cannot rephrase the question so it yields a single answer."
            },
            {
              selector: '[data-tour="add"]',
              content: "If one question is not enough to give a verdict for the claim (independent of the fact-checking article), you can add more questions. We expect you will need at least two questions for each claim, often more. Please ask all questions necessary to gather the evidence needed for the verdict, including world knowledge that might seem obvious."
            },
            {
              selector: '[data-tour="verdict"]',
              content: "Once you have collected enough question-answer pairs to give a verdict, select the most fitting option here (regardless of which verdict the fact-checking article gave). If you have not found enough information to verify or refute the claim after five minutes, please choose 'Not Enough Information' and proceed to the next claim. If the verdict relies on approximations, use your own judgment to decide if you find the claim misleading."
            },
            {
              selector: '[data-tour="submit"]',
              content: "When you have verified the claim, submit your questions and your verdict and proceed to the next article."
            },
          ];

        var current_idx = Number(localStorage.finished_qa_annotations)+1 - Number(localStorage.pc);
        var final_idx = 15;

        return (
            <QAPageDiv>
                <TourProvider steps={steps}>
                <QAPageView claim={this.state.claim}/>
                <QADataField>
                    <QuestionGenerationBar current_idx={current_idx} final_idx={final_idx} claim={this.state.claim} entries={this.state.entries} header={this.state.qa_pair_header} footer={this.state.qa_pair_footer}/>
                    <SearchField claim_date={this.state.claim.claim_date} country_code={this.state.claim.country_code}/>
                </QADataField>
                {this.state.userIsFirstVisiting? <TourWrapper/> : ""}
                </TourProvider>
            </QAPageDiv>
        );
      }
}

export default QuestionGeneration;
