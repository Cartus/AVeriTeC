import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';

const NEntryBar = styled(MetadataEntryBar)`
    width:39%;
    float:left;
`

const NPageView = styled(ClaimPageView)`
    width:59.5%;
    float:left;
    margin:10px;
    height:130vh;
`

const PageDiv = styled.div`
    width: 100%;
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