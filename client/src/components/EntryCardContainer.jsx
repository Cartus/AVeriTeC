import React from 'react';
import AddCircleIcon from '@material-ui/icons/AddCircle';
import Tooltip from '@material-ui/core/Tooltip';
import Button from '@material-ui/core/Button';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import IconButton from '@material-ui/core/IconButton';
import ClearIcon from '@material-ui/icons/Clear';
import NavBar from '../averitec_components/NavBar';

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
        

        if (this.props.headerClass != null){
          var headerString = this.props.entryName + "_header";
          var headerElem = this.newEntryDict();

          this.state = {
            entries: id,
            added_entries: this.props.numInitialEntries,
            valid: true,
            [headerString]: headerElem
          };
        } else{
          this.state = {
            entries: id,
            added_entries: this.props.numInitialEntries,
            valid: true
          };
        }

        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.deleteEntry = this.deleteEntry.bind(this);
        this.doSubmit = this.doSubmit.bind(this);
      }

    newEntryDict = () => {
        return {};
    }

    doSubmit(){
      if (this.props.validationFunction(this.state)){
        window.alert(this.props.validationFunction(this.state));
      } else{
        this.setState({
          valid: false
        });
      }

      // If valid, submit

      // If not, turn on error display
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
      if (fieldId === this.props.entryName + "_header"){
        this.setState(prevState => ({
          [fieldId]: {
                ...prevState[fieldId],
                [element]: value
            }
        }))  
      } else{
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
        const entryFields = Object.keys(this.state.entries).map(field_id => (
            <EntryCard variant="outlined">
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
            {field_id === this.props.entryName + "_entry_field_0"? "": <DeleteButton onClick={()=> this.deleteEntry(field_id)}><ClearIcon /></DeleteButton>}
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
        
        return (
            <div>
              {headerField}
                {entryFields}
                <Tooltip title={this.props.addTooltip}>
                <AddEntryCard onClick={this.addEntry} variant="outlined">
                  <AddCircleIcon/>
                </AddEntryCard>
                </Tooltip>
                <NavBar onSubmit={this.doSubmit}/>
                <div>{JSON.stringify(this.state)}</div>
            </div>
        );
      }
}

export default EntryCardContainer