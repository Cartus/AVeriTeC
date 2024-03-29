import React from 'react';
import AddCircleIcon from '@material-ui/icons/AddCircle';
import Tooltip from '@material-ui/core/Tooltip';
import Button from '@material-ui/core/Button';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import IconButton from '@material-ui/core/IconButton';
import ClearIcon from '@material-ui/icons/Clear';
import NavBar from '../averitec_components/NavBar';
import axios from "axios";
import config from "../config.json";

const EntryCard = styled(Card)`
  margin:10px;
`

const AddEntryCard = styled(EntryCard)`
text-align: center;
cursor: pointer;
padding-top: 5px;
`

const SubmitButton = styled(Button)`
float:right;
width:120px;
margin:10px !important;
`

const DeleteButton = styled(IconButton)`
  float: right;
  width:40px;
`

class EntryCardContainer extends React.Component {
  constructor(props) {
    super(props);

    this.doPrevious = this.doPrevious.bind(this);
    this.doNext = this.doNext.bind(this);
  }


  doPrevious() {
    let phase = localStorage.getItem('phase');
    localStorage.pc = Number(localStorage.pc) + 1;
    window.location.reload(false);
    // if (phase === 'phase_1') {
    //   localStorage.pc = Number(localStorage.pc) + 1;
    //   window.location.reload(false);
    // } else if (phase === 'phase_2') {
    //   localStorage.pc = Number(localStorage.pc) + 1;
    //   window.location.reload(false);
    // }
  }

  doNext() {
    let phase = localStorage.getItem('phase');
    localStorage.pc = Number(localStorage.pc) - 1;
    window.location.reload(false);
    // if (phase === 'phase_1') {
    //   localStorage.pc = Number(localStorage.pc) - 1;
    //   window.location.reload(false);
    // } else if (phase === 'phase_2') {
    //   localStorage.pc = Number(localStorage.pc) - 1;
    //   window.location.reload(false);
    // }
  }

  render() {
    let phase = localStorage.getItem('phase');

    let finished_num = 0;
    if (phase === 'phase_1') {
        finished_num = Number(localStorage.finished_norm_annotations);
    } else if (phase === 'phase_2') {
        finished_num = Number(localStorage.finished_qa_annotations);
    } else if (phase === 'phase_3') {
        finished_num = Number(localStorage.finished_valid_annotations);
    } else if (phase === 'phase_4') {
        finished_num = Number(localStorage.finished_p4_annotations);
    } else if (phase === 'phase_5') {
        finished_num = Number(localStorage.finished_p5_annotations);
    }

    if (this.props.dataset === "training"){
      if (phase === 'phase_1') {
        finished_num = Number(localStorage.train_finished_norm_annotations);
      } else if (phase === 'phase_2') {
        finished_num = Number(localStorage.train_finished_qa_annotations);
      } else if (phase === 'phase_3') {
        finished_num = Number(localStorage.train_finished_valid_annotations);
      }
    }

    console.log("finished num")
    console.log(finished_num)

    let entry_count = 0;
    let entryFields = ""
    if (this.props.entries) {
      entry_count = Object.keys(this.props.entries).length
    }

    console.log("Rendering " + entry_count + " entries.")
    console.log(this.props.entries)

    if (entry_count > 0) {
      entryFields = Object.keys(this.props.entries).map(field_id => (
        <EntryCard variant="outlined">
          {!this.props.posthocView && (entry_count > 1 || this.props.extra_entries)? <DeleteButton onClick={() => this.props.deleteEntry(field_id)}><ClearIcon /></DeleteButton> : ""}
          <this.props.contentClass
            key={field_id}
            id={field_id}
            onChange={this.props.handleFieldChange}
            valid={this.props.valid}
            data={this.props.entries[field_id]}
            removeDelete={field_id === this.props.entryName + "_entry_field_0"}
            {...this.props}
          />
        </EntryCard>
      ));
    }

    console.log("Extra entries")
    console.log(this.props.extra_entries)

    if (this.props.extra_entries) {
      let extraEntries = Object.keys(this.props.extra_entries).map(field_id => (
        <EntryCard data-tour="prev_verdict" variant="outlined">
          <DeleteButton onClick={() => this.props.deleteExtraEntry(field_id)}><ClearIcon /></DeleteButton>
          <this.props.contentClass
            key={field_id}
            id={field_id}
            onChange={this.props.handleExtraFieldChange}
            valid={true}
            data={this.props.extra_entries[field_id]}
            claim={this.props.claim}
            removeDelete={true}
            {...this.props}
          />
        </EntryCard>
      ));

      entryFields = [
        ...extraEntries,
        ...entryFields
      ]
    }
    

    if (this.props.headerClass != null) {
      var headerField = <this.props.headerClass
        key={this.props.entryName + "_header"}
        id={this.props.entryName + "_header"}
        onChange={this.props.handleFieldChange}
        valid={this.props.valid}
        data={this.props.header}
        {...this.props}
      />;
    };

    if (this.props.footerClass != null) {
      var footerField = <this.props.footerClass
        key={this.props.entryName + "_footer"}
        id={this.props.entryName + "_footer"}
        onChange={this.props.handleFieldChange}
        valid={this.props.valid}
        data={this.props.footer}
        {...this.props}
      />;
    }

    return (
      <div>
        {headerField}
        {entryFields}
        {!this.props.posthocView ?
          <Tooltip title={this.props.addTooltip}>

            <AddEntryCard data-tour="add" onClick={this.props.addEntry} variant="outlined">
              <AddCircleIcon />
            </AddEntryCard>
          </Tooltip>
          : ""
        }
        {footerField}
        {!this.props.posthocView ?
          <NavBar onPrevious={this.doPrevious} onSubmit={this.props.doSubmit} onNext={this.doNext} dataset={this.props.dataset} /> : ""
        }
      </div>
    );
  }
}

export default EntryCardContainer
