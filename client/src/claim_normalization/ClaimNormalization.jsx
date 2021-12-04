import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import axios from "axios";
import {Redirect} from "react-router-dom";
import config from "../config.json"

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
		var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/claim_norm.php",
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'reload-data',
                        offset: pc - 1
                    }
                };

                axios(request).then((response) => {
                    if (response.data) {
                        console.log(response.data);
                        const new_claim = {web_archive: response.data.web_archive};

                        localStorage.claim_id = response.data.claim_id;
                        this.setState({claim: new_claim});
                        
                        var new_entries = response.data.entries;
                        
                        for (var key in new_entries){
                            var entry = new_entries[key];
                            if (entry.date){
                                entry.date = new Date(entry.date + "T00:00:00.0Z");
                            }
                        }
                        this.setState({entries: new_entries});

                        console.log(this.state);
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {
                    window.alert(error)
                })    
            } else {
		var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/claim_norm.php",
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'next-data'
                    }
                };

                axios(request).then((response) => {
		    console.log(response.data);	
                    if (response.data) {
                        if (Number(localStorage.finished_norm_annotations) === 0) {
                            this.setState({userIsFirstVisiting: true});
                        }
                        const new_claim = {web_archive: response.data.web_archive};
                        localStorage.claim_id = response.data.claim_id;
                        this.setState({claim: new_claim});
                        const new_entries = {"claim_entry_field_0": {}};
                        this.setState({entries: new_entries});
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {
                    window.alert(error)
                })    
            }
        }
    }

    render() {

        var current_idx = 15-Number(localStorage.pc);
        var final_idx = 15;

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
              content: "Fill in the text of the main claim the article is dealing with. Please edit the claim according to the instructions, but otherwise change it as little as possible."
            },
            {
              selector: '[data-tour="verdict"]',
              content: "Select the closest verdict to the one chosen by the fact-checking article. Please mirror their verdict as well as you can, even if you disagree with it."
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
              content: "If the fact-checking article covers more than one claim, you can add additional claims. Some claims consist of multiple, easily separable, independent parts (e.g. \"The productivity rate in Scotland rose in 2017, and similarly productivity rose in Wales that year.\"). Please split these claims into their parts."
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
                    <NEntryBar current_idx={current_idx} final_idx={final_idx} entries={this.state.entries}/>
                    {this.state.userIsFirstVisiting? <TourWrapper/> : ""}
                </TourProvider>
            </PageDiv>
        );
      }
}

export default ClaimNormalization;
