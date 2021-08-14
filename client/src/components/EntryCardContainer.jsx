import React from 'react';
import AddCircleIcon from '@material-ui/icons/AddCircle';
import Tooltip from '@material-ui/core/Tooltip';
import Button from '@material-ui/core/Button';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import IconButton from '@material-ui/core/IconButton';
import ClearIcon from '@material-ui/icons/Clear';

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
        const initialString = this.props.entryName + "_entry_field_0"
        id[initialString] = this.newEntryDict()

        this.state = {
            entries: id,
            added_entries: 1
        };
        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.deleteEntry = this.deleteEntry.bind(this);
      }

    newEntryDict = () => {
        return {};
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

    render() {
        const entryFields = Object.keys(this.state.entries).map(field_id => (
            <EntryCard variant="outlined">
            <this.props.contentClass
              key={field_id}
              id={field_id}
              onChange={this.handleFieldChange}
              onDelete={this.deleteEntry}
              removeDelete={field_id === this.props.entryName + "_entry_field_0"}
            />
            {field_id === this.props.entryName + "_entry_field_0"? "": <DeleteButton onClick={()=> this.deleteEntry(field_id)}><ClearIcon /></DeleteButton>}
            </EntryCard>
          ));
        
        return (
            <div>
                {entryFields}
                <Tooltip title={this.props.addTooltip}>
                <AddEntryCard onClick={this.addEntry} variant="outlined">
                  <AddCircleIcon/>
                </AddEntryCard>
                </Tooltip>

                <SubmitButton variant="contained" color="primary" onClick={this.doSubmit}>
                  Submit
                </SubmitButton>
                <div>{JSON.stringify(this.state)}</div>
            </div>
        );
      }
}

export default EntryCardContainer