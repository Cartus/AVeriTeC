import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import * as react from "react";
import { DataGrid } from '@mui/x-data-grid';
import Button from '@material-ui/core/Button';
import DeleteIcon from '@material-ui/icons/Delete';
import axios from "axios";

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

const JsonButton = styled(Button)`
float:left;
width:200px;
margin:10px!important;
`

const DeleteButton = styled(Button)`
float:right;
width:200px;
margin:10px 0px!important;
`


class AdminControl extends react.Component {
    constructor(props) {
        super(props);

        this.state = {
            selected: []
        }

        this.cellEdit = this.cellEdit.bind(this);
        this.addRow = this.addRow.bind(this);
        this.makeNewRow = this.makeNewRow.bind(this);
        this.deleteRows = this.deleteRows.bind(this);
        this.setSelectedRows = this.setSelectedRows.bind(this);
    }

    componentDidMount() {
        if (this.props.name == "Users"){
	    var request = {
                method: "post",
                baseURL: 'https://api.averitec.eu/',
                url: "/admin_control.php",
                data:{
                    user_id: localStorage.getItem('user_id'),
                    req_type: 'get-user'
                }
            };

            axios(request).then((response) => {
                this.setState({
                    header: [
                        {field: "id", headerName: "ID", width:120},
                        {field: "user_name", headerName: "Name", editable:true, width: 250},
                        {field: "finished_norm_annotations", headerName: "Phase1 Finished", type: "number", editable: true, width: 250},
                        {field: "finished_qa_annotations", headerName: "Phase2 Finished", type: "number", editable: true, width: 250},
                        {field: "finished_valid_annotations", headerName: "Phase3 Finished", type: "number", editable: true, width: 250},
                    ],
                    table: response.data
                })
            }).catch((error) => {window.alert(error)})	
        } else if (this.props.name == "Claims"){
            this.setState({
                header: [
                    {field: "id", headerName: "ID", width:120}, 
                    {field: "claim_url", editable:true, width: 600}, 
                    {field: "phase_1_annotation_ids", editable: true, width: 250},
                    {field: "phase_2_annotation_ids", editable: true, width: 250},
                    {field: "phase_3_annotation_ids", editable: true, width: 250}
                ],
                table: [
                    {id: 0, claim_url: "https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/", phase_1_annotation_ids: [23, 1, 7], phase_2_annotation_ids: [3,4], phase_3_annotation_ids: [8]},
                ]
            })
        }
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

    deleteRows(){
        console.log(`Get JSON list file from the API containing full JSONs for these: ` + JSON.stringify(this.state.selected))
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

        let datagrid = ""

        if (this.state.header){
            datagrid = <DataGrid
                rows={this.state.table}
                columns={this.state.header}
                pageSize={10}
                rowsPerPageOptions={[10]}
                checkboxSelection
                disableSelectionOnClick
                onCellEditStop={this.cellEdit}
                onSelectionModelChange={this.setSelectedRows}
            />
        }

        var hackedDivHeight = 55 * ((this.state.table)? ((this.state.table.length > 10)? 10 : this.state.table.length) + 2 : 0) + 10

        return (
            <div className={className}>
                <EntryCard>
                    <Header>{this.props.name}</Header>
                    <div style={{ height: hackedDivHeight, width: '100%' }}>
                        {datagrid}                        
                        <AddButton variant="contained" color="primary" onClick={this.addRow}>
                            Add Row
                        </AddButton>
                        <JsonButton variant="contained" color="primary" onClick={this.downloadJsons}>
                            Download JSONs
                        </JsonButton>
                        <DeleteButton variant="contained" color="error" startIcon={<DeleteIcon />} onClick={this.deleteRows}>
                            Delete Selected
                        </DeleteButton>
                    </div>
                </EntryCard> 
            </div>
        );
    }
}

export default AdminControl;
