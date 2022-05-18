import React from 'react';
import QuestionGenerationBar from './QuestionGenerationBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import SearchField from '../components/SearchField';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import { WarningRounded } from '@material-ui/icons';
import axios from "axios";
import { Redirect } from "react-router-dom";
import config from "../config.json"
import moment from "moment";
import QuestionGenerationConfirmation from "./QuestionGenerationConfirmation"
import { notEmptyValidator, atLeastOneValidator, notBooleanValidator, emptyOrValidUrlValidator } from '../utils/validation.js'

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

const WarningDiv = styled.div`
    color:#D0342C;
    display:inline;
    position:absolute;
    margin: -5px 0px 0px -2px;
`

function validate(content) {
  var valid = true

  if (!("label" in content["qa_pair_footer"]) || notEmptyValidator(content["qa_pair_footer"]["label"]).error) {
    console.log("no label");
    valid = false;
  }

  // if("should_correct" in content["qa_pair_header"] && (!("claim_correction" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["claim_correction"]).error)){
  // if(content["qa_pair_header"]["should_correct"] === 1 && (!("claim_correction" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["claim_correction"]).error)){
  //     console.log("no correction");
  //     valid = false;
  // }

  if ("should_correct" in content["qa_pair_header"] && content["qa_pair_header"]["should_correct"] == true && (!("claim_correction" in content["qa_pair_header"]) || notEmptyValidator(content["qa_pair_header"]["claim_correction"]).error)) {
    console.log("no correction");
    valid = false;
  }

  Object.values(content["entries"]).forEach(entry => {
    if (!("question" in entry) || notEmptyValidator(entry["question"]).error) {
      console.log("no question");
      valid = false;
    }
    else if (!"answers" in entry) {
      console.log("no answers");
      valid = false;
    } else {
      entry["answers"].forEach(answer => {
        if (!("answer_type" in answer) || notEmptyValidator(answer["answer_type"]).error) {
          console.log("no answer type");
          valid = false;
        }

        if (!("source_url" in answer) || notEmptyValidator(answer["source_url"]).error || emptyOrValidUrlValidator(answer["source_url"]).error) {
          if (!("answer_type" in answer) || (answer["answer_type"] != "Unanswerable" && answer["source_medium"] != "Metadata")) {
            console.log("no source url and not unanswerable");
            valid = false;
          }
        }

        if (!("source_medium" in answer) || notEmptyValidator(answer["source_medium"]).error) {
          if (!("answer_type" in answer) || answer["answer_type"] != "Unanswerable") {
            console.log("no source medium and not unanswerable");
            valid = false;
          }
        }

        if (answer["answer_type"] == "Boolean") {
          if (!("bool_explanation" in answer) || notEmptyValidator(answer["bool_explanation"]).error) {
            console.log("boolean and no expl");
            valid = false;
          }
        } else if (notBooleanValidator(answer["answer"]).error) {
          console.log("wrong type for boolean");
          valid = false;
        }

        if (answer["answer_type"] != "Unanswerable") {
          if (!("answer" in answer) || notEmptyValidator(answer["answer"]).error) {
            console.log("no answer and not unanswerable");
            valid = false;
          }
        }
      });
    }
  });

  console.log(valid);

  return valid;
}

class QuestionGeneration extends React.Component {

  constructor(props) {
    super(props);

    this.state = {
      claim: {
        web_archive: "",
        claim_text: "",
        claim_speaker: "",
        claim_date: "",
        country_code: ""
      },
      entries: {
        "qa_pair_entry_field_0": {}
      },
      qa_pair_header: {},
      qa_pair_footer: {},
      userIsFirstVisiting: false,
      added_entries: 1,
      valid: true,
      confirmation: false,
      final_idx: 0
    }

    this.changeLabel = this.changeLabel.bind(this);
    this.doSubmit = this.doSubmit.bind(this);
    this.handleFieldChange = this.handleFieldChange.bind(this);
    this.deleteEntry = this.deleteEntry.bind(this);
    this.addEntry = this.addEntry.bind(this);
    this.proceedToConfirmation = this.proceedToConfirmation.bind(this);
    this.cancelConfirmation = this.cancelConfirmation.bind(this);
    this.shouldPreventSubmissionBecauseOfUnanswerables = this.shouldPreventSubmissionBecauseOfUnanswerables.bind(this);
  }

  shouldPreventSubmissionBecauseOfUnanswerables() {
    if (Object.values(this.state.entries).length >= 5) {
      return false;
    }

    let retval = true;
    Object.values(this.state.entries).forEach(entry => {
      if ("answers" in entry) {
        entry["answers"].forEach(answer => {
          if (answer["answer_type"] != "Unanswerable") {
            console.log("Found a good answer.")
            retval = false
            return;
          }
        });
      }
    });

    return retval;
  }

  deleteEntry = (entryId) => {
    let entries = this.state.entries
    delete entries[entryId]

    this.setState({
      entries: entries
    });
  }

  addEntry = () => {
    const field_id = "qa_pair_entry_field_" + this.state.added_entries
    console.log("adding entry \"" + field_id + "\"")

    this.setState({
      entries: {
        ...this.state.entries,
        [field_id]: {}
      },
      added_entries: this.state.added_entries + 1
    });
  }

  componentDidMount() {
    var dataset = "annotation"
    if (this.props.dataset){
        dataset = this.props.dataset
    }

    if (localStorage.getItem('login')) {
      if (this.props.finish_at){
        this.setState({
          final_idx: this.props.finish_at
        })
      } else {      
      var request = {
        method: "post",
        baseURL: config.api_url,
        url: "/user_statistics.php",
        data: {
          dataset: dataset,
          logged_in_user_id: localStorage.getItem('user_id'),
          req_type: 'get-statistics',
          get_by_user_id: localStorage.getItem('user_id')
        }
      };

      axios(request).then((response) => {
        this.setState({
          final_idx: response.data.phase_2.annotations_assigned
        })

      }).catch((error) => { window.alert(error) });
    }

      let pc = Number(localStorage.pc);
      if (pc !== 0) {
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/question_answering.php",
          data: {
            dataset: dataset,
            user_id: localStorage.getItem('user_id'),
            req_type: 'reload-data',
            offset: pc - 1
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

            localStorage.claim_norm_id = response.data.claim_norm_id;
            this.setState({ claim: new_claim });

            const new_header = { claim_correction: response.data.claim_correction, should_correct: response.data.should_correct };
            this.setState({ qa_pair_header: new_header })
            console.log(this.state.qa_pair_header);

            const new_footer = { label: response.data.label };
            this.setState({ qa_pair_footer: new_footer })

            const new_entries = response.data.entries;
            this.setState({ entries: new_entries });

            // TODO: This is a terrible hack to get around not currently storing added_entries in the DB. Please fix later.
            var new_entry_count = 0
            Object.keys(new_entries).forEach(key => {
              var n = parseInt(key.split("_").at(-1))

              new_entry_count = Math.max(new_entry_count, n)
            })
            new_entry_count += 1
            this.setState({ added_entries: new_entry_count });
            // ----------------------------------------------------------------------------------------------------------
          } else {
            window.alert("No more claims!");
          }
        }).catch((error) => { window.alert(error) })
      } else {
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/question_answering.php",
          data: {
            dataset: dataset,
            user_id: localStorage.getItem('user_id'),
            req_type: 'next-data'
          }
        };

        axios(request).then((response) => {
          if (response.data) {
            let finished_annotations = Number(localStorage.finished_qa_annotations)
            if (dataset === "training"){
                finished_annotations = Number(localStorage.train_finished_qa_annotations)
            }

            if (finished_annotations === 0) {
              this.setState({ userIsFirstVisiting: true });
            }
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

            localStorage.claim_norm_id = response.data.claim_norm_id;
            this.setState({ claim: new_claim });
            const new_entries = { "qa_pair_entry_field_0": {} };
            this.setState({ entries: new_entries });
          } else {
            window.alert("No more claims!");
          }
        }).catch((error) => { window.alert(error) })
      }
    }
  }

  setEntries = (newEntries) => {
    this.setState({
      entries: newEntries
    })
  }

  handleFieldChange(fieldId, element, value) {
    // console.log(fieldId)
    if (fieldId === "qa_pair_header") {
      this.setState(prevState => ({
        [fieldId]: {
          ...prevState[fieldId],
          [element]: value
        }
      }))
    } else if (fieldId === "qa_pair_footer") {
      this.setState(prevState => ({
        [fieldId]: {
          ...prevState[fieldId],
          [element]: value
        }
      }))
    } else {
      this.setState(prevState => ({
        entries: {
          ...prevState.entries,
          [fieldId]: {
            ...prevState.entries[fieldId],
            [element]: value
          }
        }
      }))
    }
  }

  changeLabel = event => {
    const { name, value } = event.target;

    this.setState({
      qa_pair_footer: {
        ...this.state.qa_pair_footer,
        label: value
      }
    })
  }

  proceedToConfirmation() {
    console.log("Valid: " + validate(this.state));

    if (validate(this.state)) {
      if (this.shouldPreventSubmissionBecauseOfUnanswerables()) {
        console.log(this.shouldPreventSubmissionBecauseOfUnanswerables())
        console.log("Blocked submission because all answers are unanswerable.")
        window.alert("Please try to find at least a partial answer to one of your questions. If you cannot, try to ask more questions, or rephrase and reask one of your questions.");
      } else {
        this.setState({
          confirmation: true
        });
      }
    } else {
      this.setState({
        valid: false
      });
    }
  }

  cancelConfirmation() {
    this.setState({
      confirmation: false
    });
  }

  async doSubmit() {

    var dataset = "annotation"
    if (this.props.dataset){
        dataset = this.props.dataset
    }

    let finished_annotations = Number(localStorage.finished_norm_annotations)
    if (dataset === "training"){
        finished_annotations = Number(localStorage.train_finished_norm_annotations)
    }

    var current_idx = finished_annotations + 1 - Number(localStorage.pc);

    let is_at_last_claim = current_idx === this.final_idx;
    let should_use_finish_path = this.props.finish_path && is_at_last_claim

    // e.preventDefault();
    console.log("Valid: " + validate(this.state));
    if (validate(this.state)) {
      let pc = Number(localStorage.pc);
      // console.log(pc);
      if (pc !== 0) {
        localStorage.pc = Number(localStorage.pc) - 1;
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/question_answering.php",
          data: {
            user_id: localStorage.getItem('user_id'),
            dataset: dataset,
            req_type: 'resubmit-data',
            entries: this.state.entries,
            added_entries: this.state.added_entries,
            qa_pair_header: this.state.qa_pair_header,
            qa_pair_footer: this.state.qa_pair_footer,
            claim_norm_id: localStorage.claim_norm_id
          }
        };

        await axios(request).then((response) => {
          console.log(response.data);
          localStorage.claim_norm_id = 0;

          if (should_use_finish_path) {
            console.log("redirect")
            window.location.assign(this.props.finish_path);
          } else {
            window.location.reload(false);
          }
        }).catch((error) => { window.alert(error) })
      } else {
        // console.log(this.state.added_entries);
        var request = {
          method: "post",
          baseURL: config.api_url,
          url: "/question_answering.php",
          data: {
            user_id: localStorage.getItem('user_id'),
            dataset: dataset,
            req_type: 'submit-data',
            entries: this.state.entries,
            added_entries: this.state.added_entries,
            qa_pair_header: this.state.qa_pair_header,
            qa_pair_footer: this.state.qa_pair_footer
          }
        };

        await axios(request).then((response) => {
          if (dataset === "training"){
            localStorage.train_finished_qa_annotations = Number(localStorage.train_finished_qa_annotations) + 1;
          } else {
            localStorage.finished_qa_annotations = Number(localStorage.finished_qa_annotations) + 1;
          }          
          console.log(response.data);

          if (should_use_finish_path) {
            window.location.assign(this.props.finish_path);
          } else {
            window.location.reload(false);
          }
        }).catch((error) => { window.alert(error) })
      }

    } else {
      this.setState({
        valid: false
      });
    }
  }

  render() {
    if (!localStorage.getItem('login')) {
      return <Redirect to='/' />;
    }

    var problemSourceText = <div>Searching the internet may return results from untrustworthy sources. We have compiled a list of the most common, and our search engine marks these with <WarningDiv><WarningRounded /></WarningDiv>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;. If possible, please avoid using these.</div>

    const steps = [
      {
        selector: '[data-tour="claim_text"]',
        content: "Begin by reading the claim."
      },
      {
        selector: '[data-tour="claim_page_view"]',
        content: "Carefully read the fact-checking article to see how this claim was verified."
      },
      {
        selector: '[data-tour="report"]',
        content: "If the fact-checking article shows a 404 page or another error, or if the article is behind a paywall, you can report it to us (although please give it a minute to load - some sites are not very fast)."
      },
      {
        selector: '[data-tour="question_textfield"]',
        content: "Based on the approach taken by the fact-checkers, formulate a question that will help you determine the truth of the claim. This should be a real question rather than a search engine query, e.g. \"Who is the prime minister of Britain?\" rather than \"prime minister Britain\"."
      },
      {
        selector: '[data-tour="answer_textfield"]',
        content: "Find an answer to your question. You can use any sources linked to in the fact-checking article..."
      },
      {
        selector: '[data-tour="search"]',
        content: "... or if those do not give you the answers you need, find new sources using our custom search field."
      },
      {
        selector: '[data-tour="search"]',
        content: problemSourceText
      },
      {
        selector: '[data-tour="search"]',
        content: "The search engine may also return results from other fact-checking sites. If possible, please avoid using these as well."
      },
      {
        selector: '[data-tour="search"]',
        content: "Similarly, if possible please avoid using results directly originating from the source or the speaker of the claim."
      },
      {
        selector: '[data-tour="claim_page_view"]',
        content: "WARNING: For persistence, we have stored all fact-checking articles on archive.org. Fact-checking articles may feature \"double-archived\" links using both archive.org and archive.is, e.g. \"https://web.archive.org/web/20201229212702/https://archive.md/28fMd\". Archive.org returns a 404 page for these. To view such a link, please just copy-paste the archive.is part (e.g. \"https://archive.md/28fMd\") into your browser."
      },
      {
        selector: '[data-tour="claim_page_view"]',
        content: "Links to archive.org allow you to select an archival date. Try to rely only on evidence that has appeared on the internet before the claim; e.g. with archive.org, if possible choose a date from before the claim was made."
      },
      {
        selector: '[data-tour="answer_metadata"]',
        content: "Please let us know on which page you found the answer, what kind of answer it is, and what kind of media (e.g. text, video) you found the answer in.",
      },
      {
        selector: '[data-tour="answer_type"]',
        content: "For some questions you might not be able to find an answer. That's fine - just leave the answer blank, select \"unanswerable\" as the type, and ask another question. It may be useful to ask a rephrased version of the question, or a version with more context, as the next question."
      },
      {
        selector: '[data-tour="answer_type"]',
        content: "When possible, we prefer extractive answers."
      },
      {
        selector: '[data-tour="add_answers"]',
        content: "If you find multiple different answers to a question (e.g. because you find disagreeing sources), you can write in additional answers. Please only add additional answers if you cannot rephrase the question so it yields a single answer."
      },
      {
        selector: '[data-tour="add"]',
        content: "If one question is not enough to give a verdict for the claim (independent of the fact-checking article), you can add more questions. We expect you will need at least two questions for each claim, often more. Please ask all questions necessary to gather the evidence needed for the verdict, including world knowledge that might seem obvious."
      },
      {
        selector: '[data-tour="verdict"]',
        content: "Once you have collected enough question-answer pairs to give a verdict, select the most fitting option here (regardless of which verdict the fact-checking article gave). If you have not found enough information to verify or refute the claim after five minutes, please choose 'Not Enough Information' and proceed to the next claim. If the verdict relies on approximations, use your own judgment to decide if you find the claim misleading."
      },
      {
        selector: '[data-tour="submit"]',
        content: "When you have verified the claim, submit your questions and your verdict. You will then be presented with a confirmation screen, after which you can proceed to the next article."
      },
    ];

    var dataset = "annotation"
    if (this.props.dataset){
      dataset = this.props.dataset
    }
    let finished_annotations = Number(localStorage.finished_qa_annotations)
    if (dataset === "training"){
      finished_annotations = Number(localStorage.train_finished_qa_annotations)
    }

    var current_idx = finished_annotations + 1 - Number(localStorage.pc);

    return (
      <QAPageDiv>
        {!this.state.confirmation ?
          <TourProvider steps={steps}>
            <QAPageView claim={this.state.claim} phase={2} />
            <QADataField>
              <QuestionGenerationBar
                handleFieldChange={this.handleFieldChange}
                current_idx={current_idx}
                final_idx={this.state.final_idx}
                claim={this.state.claim}
                entries={this.state.entries}
                addEntry={this.addEntry}
                deleteEntry={this.deleteEntry}
                doSubmit={this.proceedToConfirmation}
                header={this.state.qa_pair_header}
                footer={this.state.qa_pair_footer}
                valid={this.state.valid}
                dataset={dataset}
              />
              <SearchField claim_date={this.state.claim.claim_date} country_code={this.state.claim.country_code} />
            </QADataField>
            {this.state.userIsFirstVisiting ? <TourWrapper /> : ""}
          </TourProvider>
          :
          <QuestionGenerationConfirmation claim_correction={this.state.qa_pair_header? this.state.qa_pair_header.claim_correction : ""} confirmFunction={this.doSubmit} cancelFunction={this.cancelConfirmation} current_idx={current_idx} final_idx={this.state.final_idx} claim={this.state.claim} entries={this.state.entries} label={this.state.qa_pair_footer.label} footer={this.state.qa_pair_footer} changeLabel={this.changeLabel} />
        }
      </QAPageDiv>
    );
  }
}

export default QuestionGeneration;
