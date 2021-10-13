import React from 'react';
import QuestionGenerationBar from './QuestionGenerationBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import SearchField from '../components/SearchField';

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
            }
        }
      }

    render() {
        return (
            <QAPageDiv>
                <QAPageView claim={this.state.claim}/>
                <QADataField>
                    <QuestionGenerationBar claim={this.state.claim}/>
                    <SearchField claim_date={this.state.claim.claim_date} country_code={this.state.claim.country_code}/>
                </QADataField>
            </QAPageDiv>
        );
      }
}

export default QuestionGeneration;