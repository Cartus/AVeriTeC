import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';

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
                web_archive: "https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/"
            },
            userIsFirstVisiting: true
        }
      }

    render() {
        
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
                    <NEntryBar/>
                    {this.state.userIsFirstVisiting? <TourWrapper/> : ""}
                </TourProvider>
            </PageDiv>
        );
      }
}

export default ClaimNormalization;
