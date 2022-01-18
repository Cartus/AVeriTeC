import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import Button from '@material-ui/core/Button';
import axios from "axios";
import config from "../config.json";

const EntryCard = styled(Card)`
  margin:10px;
`

const ReportButton = styled(Button)`
float:right;
width:160px;
`

const ReportTextDiv = styled.div`
  float:left;

  @media (max-width: 1340px)  {
    width:100%;
  }

  @media (min-width: 1340px)  {
    width: -webkit-calc(100% - 180px);
    width:    -moz-calc(100% - 180px);
    width:         calc(100% - 180px);
  }
`

const ReportDiv = styled.div`
  float:left;
  margin:10px;
`

class ReportBar extends React.Component {
    constructor(props) {
        super(props);
        this.onReport = this.onReport.bind(this)
    }

    async onReport() {
      let phase = localStorage.getItem('phase');
      if (phase === 'phase_1') {
            var request = {
              method: "post",
              baseURL: config.api_url,
              url: "/claim_norm.php",
              data:{
                  user_id: localStorage.getItem('user_id'),
                  req_type: 'skip-data',
                  claim_id: localStorage.claim_id
              }
          };

          await axios(request).then((response) => {
              console.log(response.data);
              localStorage.finished_norm_annotations = Number(localStorage.finished_norm_annotations) + 1;
              window.location.reload(false);
          }).catch((error) => {window.alert(error)})	
      } else if (phase === 'phase_2') {
            var request = {
              method: "post",
              baseURL: config.api_url,
              url: "/question_answering.php",
              data:{
                  user_id: localStorage.getItem('user_id'),
                  req_type: 'skip-data',
                  claim_norm_id: localStorage.claim_norm_id
              }
          };

          await axios(request).then((response) => {
              console.log(response.data);
              localStorage.finished_qa_annotations = Number(localStorage.finished_qa_annotations) + 1;
              window.location.reload(false);
          }).catch((error) => {window.alert(error)})	
      }
  };

    render() {
        return (
            <EntryCard>
              <ReportDiv data-tour="report">
                <ReportTextDiv>
                If the fact checking article displays an error, is behind a paywall, or if it takes more than three minutes to load, please let us know and skip the claim.
                </ReportTextDiv>
              
              <ReportButton variant="contained" color="error" onClick={this.onReport}>
                  Report &amp; Skip
              </ReportButton>
              </ReportDiv>
            </EntryCard>
        );
    }
}

export default ReportBar;