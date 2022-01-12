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

        const id = {}; 

        for (var i = 0; i < this.props.numInitialEntries; i++) {
          var initialString = this.props.entryName + "_entry_field_" + i
          id[initialString] = this.newEntryDict()
        }        
        
        if (this.props.headerClass != null && this.props.footerClass != null){
            var headerString = this.props.entryName + "_header";
            var headerElem = this.newEntryDict();
            var footerString = this.props.entryName + "_footer";
            var footerElem = this.newEntryDict();
  
            this.state = {
              entries: id,
              added_entries: this.props.numInitialEntries,
              valid: true,
              [headerString]: headerElem,
              [footerString]: footerElem
            };
          } else if (this.props.headerClass != null){
            var headerString = this.props.entryName + "_header";
            var headerElem = this.newEntryDict();
  
            this.state = {
              entries: id,
              added_entries: this.props.numInitialEntries,
              valid: true,
              [headerString]: headerElem
            };
          }else if (this.props.footerClass != null){
            var footerString = this.props.entryName + "_footer";
            var footerElem = this.newEntryDict();
  
            this.state = {
              entries: id,
              added_entries: this.props.numInitialEntries,
              valid: true,
              [footerString]: footerElem
            };
          } else {
          this.state = {
            entries: id,
            added_entries: this.props.numInitialEntries,
            valid: true
          };
        }

        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.deleteEntry = this.deleteEntry.bind(this);
        this.doSubmit = this.doSubmit.bind(this);
        this.doPrevious = this.doPrevious.bind(this);
        this.doNext = this.doNext.bind(this);
    }

    componentWillReceiveProps (props) {
        // this.setState({entries: props.entries? props.entries : {}});
        this.setState({entries: props.entries});
        this.setState({added_entries: Object.keys(props.entries).length})
        var headerString = this.props.entryName + "_header";
        // this.setState({[headerString]: props.header? props.header : {}});
        this.setState({[headerString]: props.header});

        var footerString = this.props.entryName + "_footer";
        this.setState({[footerString]: props.footer});
        // console.log(props.footer);
    }

    newEntryDict = () => {
        return {};
    }

    async doSubmit(){
        // e.preventDefault();
        // console.log(this.props.validationFunction(this.state));
        if (this.props.validationFunction(this.state)){
            let phase = localStorage.getItem('phase');
            if (phase === 'phase_1') {
                let pc = Number(localStorage.pc);
                if (pc !== 0) {
                    localStorage.pc = Number(localStorage.pc) - 1;
		            var request = {
                        method: "post",
                        baseURL: config.api_url,
                        url: "/claim_norm.php",
                        data:{
                            user_id: localStorage.getItem('user_id'),
                            req_type: 'resubmit-data',
                            entries: this.state.entries,
                            claim_id: localStorage.claim_id
                        }
                    };

                    await axios(request).then((response) => {
                        console.log(response.data);
                        localStorage.claim_id = 0;
                        window.location.reload(false);
                    }).catch((error) => {window.alert(error)})	
                } else {
		            var request = {
                        method: "post",
                        baseURL: config.api_url,
                        url: "/claim_norm.php",
                        data:{
                            user_id: localStorage.getItem('user_id'),
                            req_type: 'submit-data',
                            entries: this.state.entries
                        }
                    };

                    await axios(request).then((response) => {
                        localStorage.finished_norm_annotations = Number(localStorage.finished_norm_annotations) + 1;
                        console.log(response.data);
                        window.location.reload(false);
                    }).catch((error) => {window.alert(error)})	
                }
            } else if (phase === 'phase_2') {
                let pc = Number(localStorage.pc);
                // console.log(pc);
                if (pc !== 0) {
                    localStorage.pc = Number(localStorage.pc) - 1;
		            var request = {
                        method: "post",
                        baseURL: config.api_url,
                        url: "/question_answering.php",
                        data:{
                            user_id: localStorage.getItem('user_id'),
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
                        window.location.reload(false);
                    }).catch((error) => {window.alert(error)})	
                } else {
                    // console.log(this.state.added_entries);
		            var request = {
                        method: "post",
                        baseURL: config.api_url,
                        url: "/question_answering.php",
                        data:{
                            user_id: localStorage.getItem('user_id'),
                            req_type: 'submit-data',
                            entries: this.state.entries,
                            added_entries: this.state.added_entries,
                            qa_pair_header: this.state.qa_pair_header,
                            qa_pair_footer: this.state.qa_pair_footer
                        }
                    };

                    await axios(request).then((response) => {
                        localStorage.finished_qa_annotations = Number(localStorage.finished_qa_annotations) + 1;
                        console.log(response.data);
                        window.location.reload(false);
                    }).catch((error) => {window.alert(error)})	
                }
            }
        } else{
            this.setState({
                valid: false
            });
        }
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
    
    deleteEntry = (entryId) => {
      let entries = this.state.entries
      delete entries[entryId]
      
      this.setState({
        entries: entries
    });
    }

    addEntry = () => {
        const field_id = this.props.entryName + "_entry_field_" + this.state.added_entries
        
        this.setState({
          entries: {
                ...this.state.entries, 
                [field_id]:this.newEntryDict()
            },
            added_entries: this.state.added_entries + 1
        });
    }

    handleFieldChange(fieldId, element, value) {
        // console.log(fieldId)
      if (fieldId === this.props.entryName + "_header"){
        this.setState(prevState => ({
          [fieldId]: {
                ...prevState[fieldId],
                [element]: value
            }
        }))  
      } else if (fieldId === this.props.entryName + "_footer"){
        this.setState(prevState => ({
          [fieldId]: {
                ...prevState[fieldId],
                [element]: value
            }
        }))  
      }else{
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

    render() {
      console.log("Rendering " + this.state.added_entries + " entries.")
        const entryFields = Object.keys(this.state.entries).map(field_id => (
            <EntryCard variant="outlined">
            {this.state.added_entries > 1? <DeleteButton onClick={()=> this.deleteEntry(field_id)}><ClearIcon /></DeleteButton> : ""}
            <this.props.contentClass
              key={field_id}
              id={field_id}
              onChange={this.handleFieldChange}
              onDelete={this.deleteEntry}
              valid={this.state.valid}
              data={this.state.entries[field_id]}
              removeDelete={field_id === this.props.entryName + "_entry_field_0"}
              {...this.props}
            />
            </EntryCard>
          ));

        if (this.props.headerClass != null){
          var headerField = <this.props.headerClass
          key={this.props.entryName + "_header"}
          id={this.props.entryName + "_header"}
          onChange={this.handleFieldChange}
          valid={this.state.valid}
          data={this.state[this.props.entryName + "_header"]}
          {...this.props}
          />;
        };

        if (this.props.footerClass != null){
            var footerField = <this.props.footerClass
            key={this.props.entryName + "_footer"}
            id={this.props.entryName + "_footer"}
            onChange={this.handleFieldChange}
            valid={this.state.valid}
            data={this.state[this.props.entryName + "_footer"]}
            {...this.props}
            />;
        }
        
        return (
            <div>
              {headerField}
                {entryFields}
                <Tooltip title={this.props.addTooltip}>
                <AddEntryCard data-tour="add" onClick={this.addEntry} variant="outlined">
                  <AddCircleIcon/>
                </AddEntryCard>
                </Tooltip>
                {footerField}
                {/*{JSON.stringify(this.state)}*/}
                <NavBar onPrevious={this.doPrevious} onSubmit={this.doSubmit} onNext={this.doNext}/>
            </div>
        );
      }
}

export default EntryCardContainer
