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

  async onReport(skip_reason) {
    var dataset = "annotation"
    if (this.props.dataset) {
      dataset = this.props.dataset
    }
    console.log("I'm skipping for dataset: " + dataset)

    let phase = localStorage.getItem('phase');
    if (phase === 'phase_1') {
      console.log("Claim id:")
      console.log(localStorage.claim_id)
      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/claim_norm.php",
        data: {
          dataset: dataset,
          user_id: localStorage.getItem('user_id'),
          req_type: 'skip-data',
          claim_id: localStorage.claim_id,
          skip_reason: skip_reason
        }
      };

      await axios(request).then((response) => {
        console.log(response.data);
        if (dataset === "training") {
          localStorage.train_finished_norm_annotations = Number(localStorage.train_finished_norm_annotations) + 1;
        } else {
          localStorage.finished_norm_annotations = Number(localStorage.finished_norm_annotations) + 1;
        }
        window.location.reload(false);
      }).catch((error) => { window.alert(error) })
    } else if (phase === 'phase_2') {
      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/question_answering.php",
        data: {
          dataset: dataset,
          user_id: localStorage.getItem('user_id'),
          req_type: 'skip-data',
          claim_norm_id: localStorage.claim_norm_id,
          skip_reason: skip_reason
        }
      };

      console.log("I'm skipping the claim with norm id " + localStorage.claim_norm_id)

      await axios(request).then((response) => {
        console.log(response.data);

        if (dataset === "training") {
          localStorage.train_finished_qa_annotations = Number(localStorage.train_finished_qa_annotations) + 1;
        } else {
          localStorage.finished_qa_annotations = Number(localStorage.finished_qa_annotations) + 1;
        }

        window.location.reload(false);
      }).catch((error) => { window.alert(error) })
    }
  };

  render() {
    let phase = localStorage.getItem('phase');
    var extra_media_skipper = ""
    if (phase === 'phase_2' || phase === 'phase_4') {
      extra_media_skipper = <EntryCard>
        <ReportDiv data-tour="report">
          <ReportTextDiv>
            If the claim <b>directly refers</b> to an image, video, or audio clip, and <b>cannot be understood</b> without viewing or hearing it, the claim is a media claim that erroneously passed Phase One. In that case, please use this button to report the claim to use and skip it. Please be aware you cannot report a claim if you have already submitted an annotation for it.
          </ReportTextDiv>

          <ReportButton variant="contained" disabled={Number(localStorage.pc) > 0} color="error" onClick={() => this.onReport("media")}>
            Report Media Claim
          </ReportButton>
        </ReportDiv>
      </EntryCard>
    }

    return (
      <div>
      <EntryCard>
        <ReportDiv data-tour="report">
          <ReportTextDiv>
            If the fact checking article displays an error, is behind a paywall, or if it takes more than three minutes to load, please let us know and skip the claim. Please be aware you cannot report a claim if you have already submitted an annotation for it.
          </ReportTextDiv>

          <ReportButton variant="contained" disabled={Number(localStorage.pc) > 0} color="error" onClick={() => this.onReport("other")}>
            Report &amp; Skip
          </ReportButton>
        </ReportDiv>
      </EntryCard>
      {extra_media_skipper}
      </div>
    );
  }
}

export default ReportBar;