import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';

import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import { DataGrid } from '@mui/x-data-grid';
import Button from '@material-ui/core/Button';
import { isThisSecond } from 'date-fns';

const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px 5px 20px;
`

const Header = styled.h4`
`

const AddButton = styled(Button)`
float:left;
width:200px;
margin:10px 0px!important;
`

const DeleteButton = styled(Button)`
float:right;
width:200px;
margin:10px 0px!important;
`


class AdminControl extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            header: [
                {field: "id", headerName: "ID", width:120}, 
                {field: "Username", editable:true, width: 250}, 
                {field: "randomNumber", type: "number", editable: true, width: 250}
            ],
            table: [
                {id: 0, Username: "Michael", "randomNumber": 348},
                {id: 1, Username: "Zhijiang", "randomNumber": 241},
                {id: 2, Username: "Andreas", "randomNumber": 115},
            ],
            selected: []
        }

        this.cellEdit = this.cellEdit.bind(this);
        this.addRow = this.addRow.bind(this);
        this.makeNewRow = this.makeNewRow.bind(this);
        this.deleteRows = this.deleteRows.bind(this);
        this.setSelectedRows = this.setSelectedRows.bind(this);
    }

    cellEdit(params, event) {
        console.log(`Editing cell with value: ${params.value} and row id: ${params.id}, column: ${params.field}, triggered by ${event.type}.`)
        // I did not implement code to edit the state here. We shjould make the API call to edit instead, then reload the entire table. That way, if there is a mistake/lost connection to the server/etc, the state will not falsely update.
    }

    makeNewRow(){
        // This should be an API call
        return {id: this.state.table.length}
    }

    deleteRows(){
        console.log(`Delete entries by ID: ` + JSON.stringify(this.state.selected))
        // I did not implement code to delete from the state here. We shjould make the API call to edit instead, then reload the entire table. That way, if there is a mistake/lost connection to the server/etc, the state will not falsely update.
    }

    setSelectedRows(rows){
        this.setState({
            selected: rows
        })
    }

    addRow(){
        this.setState({
            table: [
                  ...this.state.table, 
                  this.makeNewRow()
            ]
        })
    }

    render() {
        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }
        
        return (
            <div className={className}>
                <EntryCard>
                    <Header>{this.props.name}</Header>
                    <div style={{ height: 400, width: '100%' }}>
                        <DataGrid
                            rows={this.state.table}
                            columns={this.state.header}
                            pageSize={5}
                            rowsPerPageOptions={[5]}
                            checkboxSelection
                            disableSelectionOnClick
                            onCellEditStop={this.cellEdit}
                            onSelectionModelChange={this.setSelectedRows}
                        />
                        <AddButton variant="contained" color="secondary" onClick={this.addRow}>
                            Add Row
                        </AddButton>
                        <DeleteButton variant="contained" color="primary" onClick={this.deleteRows}>
                            Delete Selected
                        </DeleteButton>
                    </div>
                </EntryCard> 
                {JSON.stringify(this.state)}
            </div>
        );
    }
}

export default AdminControl;