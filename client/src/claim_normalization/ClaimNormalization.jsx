import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import axios from "axios";
import {Redirect} from "react-router-dom";

const NEntryBar = styled(MetadataEntryBar)`
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

const NPageView = styled(ClaimPageView)`
    width:60%;
    float:left;
    border-style:inset;
    border-width:2px;
    height: -webkit-calc(100% - 6px)!important;
    height:    -moz-calc(100% - 6px)!important;
    height:         calc(100% - 6px)!important;
`

const PageDiv = styled.div`
    width: 100%;
    height: 100vh;
`

class ClaimNormalization extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            claim : {
                web_archive: ""
            },
            // entries : {"claim_entry_field_1":{}},
            entries : {},
            userIsFirstVisiting: false
        }
    }

    componentDidMount() {
        if (localStorage.getItem('login')) {
            let pc = Number(localStorage.pc);
            console.log(pc);
            if (pc !== 0) {
                axios({
                    method: 'post',
                    url: "http://localhost:8081/api/claim_norm.php",
                    headers: {'content-type': 'application/json'},
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'reload-data',
                        offset: pc - 1
                    }
                })
                    .then(result => {
                        if (result.data) {
                            console.log(result.data);
                            const new_claim = {web_archive: result.data.web_archive};
                            localStorage.claim_id = result.data.claim_id;
                            this.setState({claim: new_claim});
                            const new_entries = result.data.entries;
                            this.setState({entries: new_entries});
                            console.log(this.state);
                        } else {
                            window.alert("No more claims!");
                        }

                    })
                    .catch(error => this.setState({error: error.message}));
            } else {
                axios({
                    method: 'post',
                    url: "http://localhost:8081/api/claim_norm.php",
                    headers: {'content-type': 'application/json'},
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'next-data'
                    }
                })
                    .then(result => {
                        console.log(result.data);
                        if (result.data) {
                            if (Number(localStorage.finished_norm_annotations) === 0) {
                                this.setState({userIsFirstVisiting: true});
                            }
                            const new_claim = {web_archive: result.data.web_archive};
                            localStorage.claim_id = result.data.claim_id;
                            this.setState({claim: new_claim});
                            const new_entries = {"claim_entry_field_0":{}};
                            this.setState({entries: new_entries});
                        } else {
                            window.alert("No more claims!");
                        }
                    })
                    .catch(error => this.setState({error: error.message}));
            }
        }
    }

    render() {

        if (!localStorage.getItem('login')) {
            return <Redirect to='/'/>;
        }
        
        const steps = [
            {
              selector: '[data-tour="claim_page_view"]',
              content: "Begin by carefully reading the fact-checking article."
            },
            {
              selector: '[data-tour="report"]',
              content: "If the fact-checking article shows a 404 page or another error, or if the article is behind a paywall, you can report it to us (although please give it a minute to load - some sites are not very fast)."
            },
            {
              selector: '[data-tour="claim_textfield"]',
              content: "Fill in the text of the main claim the article is dealing with."
            },
            {
              selector: '[data-tour="verdict"]',
              content: "Select the closest verdict to the one chosen by the fact-checking article."
            },
            {
              selector: '[data-tour="metadata_fields"]',
              content: "Using the fact-checking article, fill in the rest of the data collection fields..."
            },
            {
              selector: '[data-tour="metadata_fields_2"]',
              content: "... and select a the most appropriate claim types and fact checking strategies."
            },
            {
              selector: '[data-tour="add"]',
              content: "If the fact-checking article covers more than one claim, you can add additional claims."
            },
            {
              selector: '[data-tour="submit"]',
              content: "When you have added all claims from this fact-checking article, submit your claims to proceed to the next article."
            },
          ];

        return (
            <PageDiv>
                <TourProvider steps={steps}>
                    <NPageView claim={this.state.claim}/>
                    <NEntryBar entries={this.state.entries}/>
                    {this.state.userIsFirstVisiting? <TourWrapper/> : ""}
                </TourProvider>
            </PageDiv>
        );
      }
}

export default ClaimNormalization;
