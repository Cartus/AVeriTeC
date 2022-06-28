import React from 'react';
import AppBar from '@mui/material/AppBar';
import Toolbar from '@mui/material/Toolbar';
import Typography from '@mui/material/Typography';
import styled from 'styled-components';
import MetadataEntryBar from '../claim_normalization/MetadataEntryBar';
import axios from "axios";
import config from "../config.json"
import Card from '@material-ui/core/Card';
import moment from "moment";
import QuestionGenerationBar from "../question_generation/QuestionGenerationBar"
import VerdictValidationBar from '../verdict_validation/VerdictValidationBar';
import IconButton from '@material-ui/core/IconButton';
import NavigateNextIcon from '@material-ui/icons/NavigateNext';
import NavigateBeforeIcon from '@material-ui/icons/NavigateBefore';

const AnnotationView = styled("div")`
    width: -webkit-calc(50% - 4px)!important;
    width:    -moz-calc(50% - 4px)!important;
    width:         calc(50% - 4px)!important;
    float:left;
    border-style:inset;
    border-width:2px;
    height: -webkit-calc(100vh - 68px)!important;
    height:    -moz-calc(100vh - 68px)!important;
    height:         calc(100vh - 68px)!important;
    overflow: auto;
`

const WhiteLink = styled.a`
  color:white;
`

const NavButton = styled(IconButton)`
  width:55px;
  height:53px;
`
const PrevButton = styled(NavButton)`
  float: left;
`
const NextButton = styled(NavButton)`
  float: right;
`

const EntryCard = styled(Card)`
  margin:10px;
  text-align:center;
`

const BarPartBox = styled("div")`
  width: 100%;
  float:left;
`

const PaddingTypographBox = styled(Typography)`
  padding: 10px 24px 10px 134px;
  width: -webkit-calc(100% - 398px)!important;
  width:    -moz-calc(100% - 398px)!important;
  width:         calc(100% - 398px)!important;
  float:left;
  text-align:center;
`

const ContinueTypographBox = styled(Typography)`
  padding: 10px 24px;
  float:right;
  color:white;
  text-decoration:underline;
`

const ShoveBox = styled("div")`
  width: 1px;
  height: 64px;
`

const SepBox = styled("div")`
  width: 100%;
  height: 64px;
`

const P3SepDiv = styled("div")`
  width: 100%;
  height: 1px;
  margin: -11px;
  clear:both;
`

class TrainingOverlay extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      shown_annotation_id: 0,
      shown_annotation: {

      },
      gold_annotations: [

      ]
    }

    this.moveForward = this.moveForward.bind(this);
    this.moveBackward = this.moveBackward.bind(this);
  }

  componentDidMount() {
    console.log("Loading training overview for phase " + this.props.phase)
    this.fillPhase()
  }

  fillPhase() {
    if (this.props.phase == 1) {
      this.fillPhaseOneData()
    } else if (this.props.phase == 2) {
      this.fillPhaseTwoData()
    } else if (this.props.phase == 3) {
      this.fillPhaseThreeData()
    }
  }

  canMoveBackward() {
    return this.state.shown_annotation_id > 0
  }

  canMoveForward() {
    let trainingClaimsInPhase = 0
    if (this.props.phase == 1) {
      trainingClaimsInPhase = Number(localStorage.train_finished_norm_annotations)
    } else if (this.props.phase == 2) {
      trainingClaimsInPhase = Number(localStorage.train_finished_qa_annotations)
    } else if (this.props.phase == 3) {
      trainingClaimsInPhase = Number(localStorage.train_finished_valid_annotations)
    }

    console.log("training claims:")
    console.log(trainingClaimsInPhase)

    return this.state.shown_annotation_id + 1 < trainingClaimsInPhase
  }

  moveForward() {
    if (this.canMoveForward()) {
      console.log("Moving to claim " + (this.state.shown_annotation_id + 1))
      this.setState({
        shown_annotation_id: this.state.shown_annotation_id + 1
      }, this.fillPhase)
    }
  }

  moveBackward() {
    if (this.canMoveBackward()) {
      console.log("Moving to claim " + (this.state.shown_annotation_id - 1))
      this.setState({
        shown_annotation_id: this.state.shown_annotation_id - 1
      }, this.fillPhase)
    }
  }

  fillPhaseThreeData() {
    if (localStorage.getItem('login')) {
      console.log("p3 info")
      console.log(this.state.shown_annotation_id);
      console.log(Number(localStorage.train_finished_valid_annotations) - 1 - this.state.shown_annotation_id)

      var otherUserId = new URLSearchParams(window.location.search).get("id")
      var shownUserId = localStorage.getItem('user_id');
      if (otherUserId){
        console.log("Getting data for user with ID \'" + otherUserId + "\'.")

        // Use this to test if we're logged in with an admin account:
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/user_statistics.php",
          data: {
              logged_in_user_id: localStorage.getItem('user_id'),
              req_type: 'get-statistics',
              get_by_user_id: otherUserId
          }
        };

        axios(request).then((response) => {
          console.log(response.data)
          if (!response.data.is_admin) {
            window.alert("Error: Access denied.")
            window.location.replace("/control");
          }else{
            shownUserId = otherUserId;
          }}
        );
      }         

      // Load annotator data:

      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/verdict_validate.php",
        data: {
          user_id: shownUserId,
          dataset: 'training', // I set it up like this to make code reuse easier. Is it right?
          req_type: 'reload-data',
          offset: Number(localStorage.train_finished_valid_annotations) - 1 - this.state.shown_annotation_id
        }
      };

      axios(request).then((response) => {
        if (response.data) {
          console.log("Recevied response")
          console.log(response.data);
          const new_claim = {
            web_archive: response.data.web_archive,
            claim_text: response.data.claim_text,
            claim_speaker: response.data.claim_speaker,
            claim_source: response.data.claim_source,
            claim_hyperlink: response.data.claim_hyperlink,
            claim_date: response.data.claim_date,
            questions: response.data.questions,
            country_code: response.data.country_code
          };
          localStorage.claim_norm_id = response.data.claim_norm_id;

          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              claim: new_claim,
              annotation: response.data.annotation
            }
          });
        } else {
          window.alert("No more claims!");
        }
      }).catch((error) => { window.alert(error) })


      // Load gold annotations:
      // TODO this is just dummy data

      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/training_verdict_validate.php",
        data: {
          user_id: shownUserId,
          req_type: 'load-data',
          offset: this.state.shown_annotation_id
        }
      };

      axios(request).then((response) => {
        if (response.data) {
          console.log("Recevied p3 response")
          console.log(response.data);
          console.log(response.data.annotations)          

          var gold_annotations = response.data.annotations.map(a => {
            let new_claim = {
              web_archive: a.web_archive,
              claim_text: a.claim_text,
              claim_speaker: a.claim_speaker,
              claim_source: a.claim_source,
              claim_hyperlink: a.claim_hyperlink,
              claim_date: a.claim_date,
              questions: a.questions,
              country_code: a.country_code
            };
            localStorage.claim_norm_id = a.claim_norm_id;
  
            let annotation_dict = {
              claim: new_claim,
              annotation: a.annotation
            }

            return annotation_dict
          });          

          this.setState({
            gold_annotations: gold_annotations
          });

        } else {
          window.alert("No more claims!");
        }
      }).catch((error) => { window.alert(error) })
    }
  }

  fillPhaseTwoData() {
    if (localStorage.getItem('login')) {
      console.log(this.state.shown_annotation_id);

      var otherUserId = new URLSearchParams(window.location.search).get("id")
      var shownUserId = localStorage.getItem('user_id');
      if (otherUserId){
        console.log("Getting data for user with ID \'" + otherUserId + "\'.")

        // Use this to test if we're logged in with an admin account:
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/user_statistics.php",
          data: {
              logged_in_user_id: localStorage.getItem('user_id'),
              req_type: 'get-statistics',
              get_by_user_id: otherUserId
          }
        };

        axios(request).then((response) => {
          console.log(response.data)
          if (!response.data.is_admin) {
            window.alert("Error: Access denied.")
            window.location.replace("/control");
          }else{
            shownUserId = otherUserId;
          }}
        );
      }     

      // Load annotator data for training examples:

      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/question_answering.php",
        data: {
          user_id: shownUserId,
          dataset: 'training', // I set it up like this to make code reuse easier. Is it right?
          req_type: 'reload-data',
          offset: Number(localStorage.train_finished_qa_annotations) - 1 - this.state.shown_annotation_id
        }
      };

      axios(request).then((response) => {
        if (response.data) {
          console.log(response.data);
          const new_claim = {
            web_archive: response.data.web_archive,
            claim_text: response.data.cleaned_claim,
            claim_speaker: response.data.speaker,
            claim_date: response.data.check_date,
            country_code: response.data.country_code,
            claim_source: response.data.claim_source
          };

          if (new_claim.claim_date) {
            var claim_date = new Date(new_claim.claim_date + "T00:00:00.0Z");
            new_claim.claim_date = moment(claim_date).format('DD/MM/YYYY');
          }

          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              claim: new_claim
            }
          });

          const new_header = { claim_correction: response.data.claim_correction, should_correct: response.data.should_correct };
          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              qa_pair_header: new_header
            }
          });

          const new_footer = { label: response.data.label };
          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              qa_pair_footer: new_footer
            }
          });

          const new_entries = response.data.entries;
          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              entries: new_entries
            }
          });
        } else {
          window.alert("No more claims!");
        }
      }).catch((error) => { window.alert(error) })

      // Load gold annotations:

      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/training_question_answering.php",
        data: {
          user_id: shownUserId,
          req_type: 'load-data',
          offset: this.state.shown_annotation_id
        }
      };

      axios(request).then((response) => {
        if (response.data) {
          console.log(response.data);

          var gold_annotations = response.data.annotations.map(a => {
            const new_claim = {
              web_archive: a.web_archive,
              claim_text: a.cleaned_claim,
              claim_speaker: a.speaker,
              claim_date: a.check_date,
              country_code: a.country_code,
              claim_source: a.claim_source
            };
  
            if (new_claim.claim_date) {
              var claim_date = new Date(new_claim.claim_date + "T00:00:00.0Z");
              new_claim.claim_date = moment(claim_date).format('DD/MM/YYYY');
            }
  
            let annotation_dict = {}
            annotation_dict.claim = new_claim;
  
            let new_header = { claim_correction: a.claim_correction, should_correct: a.should_correct };
            annotation_dict.qa_pair_header = new_header;
  
            let new_footer = { label: a.label };
            annotation_dict.qa_pair_footer = new_footer;
  
            let new_entries = a.entries;
            annotation_dict.entries = new_entries;

            return annotation_dict
          });          

          this.setState({
            gold_annotations: gold_annotations
          });

        } else {
          window.alert("No more claims!");
        }
      }).catch((error) => { window.alert(error) })
    }
  }

  fillPhaseOneData() {
    if (localStorage.getItem('login')) {
      console.log("Claim id")
      console.log(this.state.shown_annotation_id);
      console.log(Number(localStorage.train_finished_norm_annotations) - 1 - this.state.shown_annotation_id);

      var otherUserId = new URLSearchParams(window.location.search).get("id")
      var shownUserId = localStorage.getItem('user_id');
      if (otherUserId){
        console.log("Getting data for user with ID \'" + otherUserId + "\'.")

        // Use this to test if we're logged in with an admin account:
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/user_statistics.php",
          data: {
              logged_in_user_id: localStorage.getItem('user_id'),
              req_type: 'get-statistics',
              get_by_user_id: otherUserId
          }
        };

        axios(request).then((response) => {
          console.log(response.data)
          if (!response.data.is_admin) {
            window.alert("Error: Access denied.")
            window.location.replace("/control");
          }else{
            shownUserId = otherUserId;
          }}
        );
      }     

      // Load annotator training data:

      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/claim_norm.php",
        data: {
          user_id: shownUserId,
          dataset: 'training', // I set it up like this to make code reuse easier. Is it right?
          req_type: 'reload-data',
          offset: Number(localStorage.train_finished_norm_annotations) - 1 - this.state.shown_annotation_id
        }
      };

      axios(request).then((response) => {
        if (response.data) {
          console.log("Received p1 user response:")
          console.log(response.data);
          const new_claim = {
            web_archive: response.data.web_archive
          };

          localStorage.claim_id = response.data.claim_id;
          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              claim: new_claim
            }
          });

          var new_entries = response.data.entries;

          for (var key in new_entries) {
            var entry = new_entries[key];
            if (entry.date) {
              entry.date = new Date(entry.date + "T00:00:00.0Z");
            }
          }
          this.setState({
            shown_annotation: {
              ...this.state.shown_annotation,
              entries: new_entries
            }
          });

          console.log(this.state);
        } else {
          window.alert("No more claims!");
        }
      }).catch((error) => {
        console.log("Error loading phase one claim")
        window.alert(error)
      })

      // Load gold annotation data:
      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/training_claim_norm.php",
        data: {
          user_id: shownUserId,
          req_type: 'load-data',
          offset: this.state.shown_annotation_id
        }
      };

      axios(request).then((response) => {
        if (response.data) {
          console.log("Received response:")
          console.log(response.data);

          var gold_annotations = response.data.annotations.map(a => {
            const new_claim = { web_archive: a.web_archive };
            localStorage.claim_id = a.claim_id;
            let annotation_dict = {}

            annotation_dict["claim"] = new_claim

            var new_entries = a.entries;

            for (var key in new_entries) {
              var entry = new_entries[key];
              if (entry.date) {
                entry.date = new Date(entry.date + "T00:00:00.0Z");
              }
            }

            annotation_dict["entries"] = new_entries

            return annotation_dict
          });          

          this.setState({
            gold_annotations: gold_annotations
          });

          console.log(this.state);
        } else {
          window.alert("No more claims!");
        }
      }).catch((error) => {
        console.log("Error loading phase one claim")
        window.alert(error)
      })
    }
  }

  render() {
    var current_idx = 0
    var final_idx = 0

    console.log(this.state)

    let annotator_view = ""
    let gold_view = ""

    if (this.props.phase === 1) {
      let entries = this.state.shown_annotation.entries;
      if (!entries) {
        entries = {}
      }

      annotator_view = <MetadataEntryBar posthocView={true} current_idx={current_idx} final_idx={final_idx} entries={entries} />

      gold_view = this.state.gold_annotations.map((annotation, index) => {
        let entries = annotation.entries;
        if (!entries) {
          entries = {}
        }
        return <div>
          <SepBox>
            <EntryCard>
              <h3>Suggested Annotation #{index + 1}</h3>
            </EntryCard>
          </SepBox>
          <MetadataEntryBar posthocView={true} entries={entries} />
        </div>
      })
    } else if (this.props.phase === 2) {
      annotator_view = <QuestionGenerationBar posthocView={true} current_idx={current_idx} final_idx={final_idx} entries={this.state.shown_annotation.entries} claim={this.state.shown_annotation.claim} header={this.state.shown_annotation.qa_pair_header} footer={this.state.shown_annotation.qa_pair_footer} />

      gold_view = this.state.gold_annotations.map((annotation, index) => {
        return <div>
          <SepBox>
            <EntryCard>
              <h3>Suggested Annotation #{index + 1}</h3>
            </EntryCard>
          </SepBox>
          <QuestionGenerationBar posthocView={true} entries={annotation.entries} claim={annotation.claim} header={annotation.qa_pair_header} footer={annotation.qa_pair_footer} />
        </div>
      })
    } else if (this.props.phase === 3) {
      annotator_view = <VerdictValidationBar
        posthocView={true}
        current_idx={current_idx}
        final_idx={final_idx}
        claim={this.state.shown_annotation.claim}
        annotation={this.state.shown_annotation.annotation}
      />

      gold_view = this.state.gold_annotations.map((annotation, index) => {
        return <div>
          <SepBox>
            <EntryCard>
              <h3>Suggested Annotation #{index + 1}</h3>
            </EntryCard>
          </SepBox>
          <VerdictValidationBar
            posthocView={true}
            claim={annotation.claim}
            annotation={annotation.annotation}
          />
          <P3SepDiv />
        </div>
      })
    }

    return (
      <div>
        <AppBar>
          <Toolbar>
            <BarPartBox>
              {this.canMoveBackward() ?
                <PrevButton onClick={this.moveBackward}><NavigateBeforeIcon fontSize="large" style={{ color: 'white' }} /></PrevButton>
                :
                <PrevButton onClick={() => { }}><NavigateBeforeIcon fontSize="large" disabled style={{ color: 'grey' }} /></PrevButton>
              }
              <PaddingTypographBox variant="h6" component="div">
                Training Annotation Overview | Phase {this.props.phase} | Example {this.state.shown_annotation_id + 1}
              </PaddingTypographBox>
              {this.canMoveForward() ?
                <NextButton onClick={this.moveForward}><NavigateNextIcon fontSize="large" style={{ color: 'white' }} /></NextButton>
                :
                this.props.finish_path? 
                <WhiteLink href={this.props.finish_path}><ContinueTypographBox variant="h6" component="div">Continue</ContinueTypographBox></WhiteLink> : 
                <WhiteLink href={"/control"}><ContinueTypographBox variant="h6" component="div">Exit</ContinueTypographBox></WhiteLink>
              }
            </BarPartBox>
          </Toolbar>
        </AppBar>
        <ShoveBox />

        <AnnotationView>
          <EntryCard>
            <h3>Your Annotation</h3>
          </EntryCard>
          {annotator_view}
        </AnnotationView>
        <AnnotationView>
          {gold_view}
        </AnnotationView>
      </div>
    );
  }
}

export default TrainingOverlay