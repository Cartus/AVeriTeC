import React from 'react';
import QuestionGenerationBar from './QuestionGenerationBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import SearchField from '../components/SearchField';


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
                claim_date: "15/06/2021"
            }
        }
      }

    render() {
        const pageViewStyle = {width: "60%", float: "left"};
        const searchFieldStyle = {width: "39.5%", float: "left"};
        return (
            <div>
                <QuestionGenerationBar claim={this.state.claim}/>
                <div>
                <ClaimPageView claim={this.state.claim} style={pageViewStyle}/>
                <SearchField style={searchFieldStyle}/>
                </div>
            </div>
        );
      }
}

export default QuestionGeneration;