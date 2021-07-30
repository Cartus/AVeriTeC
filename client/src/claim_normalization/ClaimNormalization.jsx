import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';

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
            <div>
                <MetadataEntryBar/>
                <ClaimPageView claim={this.state.claim}/>
            </div>
        );
      }
}

export default ClaimNormalization;