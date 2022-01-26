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


    doPrevious(){
        let phase = localStorage.getItem('phase');
        if (phase === 'phase_1') {
            localStorage.pc = Number(localStorage.pc) + 1;
            window.location.reload(false);
        } else if (phase === 'phase_2') {
            localStorage.pc = Number(localStorage.pc) + 1;
            window.location.reload(false);
        }
    }

    doNext() {
        let phase = localStorage.getItem('phase');
        if (phase === 'phase_1') {
            localStorage.pc = Number(localStorage.pc) - 1;
            window.location.reload(false);
        } else if (phase === 'phase_2') {
            localStorage.pc = Number(localStorage.pc) - 1;
            window.location.reload(false);
        }
    }
    
    render() {
      console.log("Rendering " + Object.keys(this.props.entries).length + " entries.")
        const entryFields = Object.keys(this.props.entries).map(field_id => (
            <EntryCard variant="outlined">
            {Object.keys(this.props.entries).length > 1? <DeleteButton onClick={()=> this.props.deleteEntry(field_id)}><ClearIcon /></DeleteButton> : ""}
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

        if (this.props.headerClass != null){
          var headerField = <this.props.headerClass
          key={this.props.entryName + "_header"}
          id={this.props.entryName + "_header"}
          onChange={this.props.handleFieldChange}
          valid={this.props.valid}
          data={this.props.header}
          {...this.props}
          />;
        };

        if (this.props.footerClass != null){
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
                <Tooltip title={this.props.addTooltip}>
                <AddEntryCard data-tour="add" onClick={this.props.addEntry} variant="outlined">
                  <AddCircleIcon/>
                </AddEntryCard>
                </Tooltip>
                {footerField}
                <NavBar onPrevious={this.doPrevious} onSubmit={this.props.doSubmit} onNext={this.doNext}/>
            </div>
        );
      }
}

export default EntryCardContainer
