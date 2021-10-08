import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';

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
            }
        }
      }

    render() {
        return (
            <PageDiv>
                <NPageView claim={this.state.claim}/>
                <NEntryBar/>
            </PageDiv>
        );
      }
}

export default ClaimNormalization;