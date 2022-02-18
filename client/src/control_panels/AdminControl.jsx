import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import * as react from "react";
import { DataGrid } from '@mui/x-data-grid';
import Button from '@material-ui/core/Button';
import DeleteIcon from '@material-ui/icons/Delete';
import axios from "axios";
import config from "../config.json"
import Slider from '@mui/material/Slider';
import Radio from '@mui/material/Radio';
import RadioGroup from '@mui/material/RadioGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import FormControl from '@mui/material/FormControl';
import FormLabel from '@mui/material/FormLabel';
import { TextField } from '@mui/material';
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip, LineChart, Line, Legend, ResponsiveContainer,
    BarChart, Bar, Cell
} from "recharts";

const AssignChartBox = styled("div")`
    float: right;
    width: 30%;
    height: 0px;
`

const AssignRadioBox = styled("div")`
    float: left;
    width: 30%;
`

const AssignControlBox = styled("div")`
    float: left;
    width: 100%;
`

const AssignControlTextBox = styled("div")`
    float: left;
    width: 100%;
    margin: 10px 0px;
    line-height: 210%;
`

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
        if (this.props.name == "Users") {
            var request = {
                method: "post",
                baseURL: config.api_url,
                url: "/admin_control.php",
                data: {
                    user_id: localStorage.getItem('user_id'),
                    req_type: 'get-user'
                }
            };

            axios(request).then((response) => {
                var user_data = response.data
                console.log("User data")
                console.log(user_data)

                // Please update this: 
                user_data = user_data.map(user_dict => {
                    let new_dict = user_dict
                    new_dict["p1_task_time"] = 8.2
                    new_dict["p2_task_time"] = 17.5
                    return new_dict
                });
                // ----------- 

                this.setState({
                    header: [
                        {
                            field: "id",
                            headerName: "ID",
                            width: 120,
                            renderCell: (cellValues) => {
                                return <a href={`/user?id=${cellValues.row.id}`}>{cellValues.row.id}</a>;
                            }
                        },
                        { field: "user_name", headerName: "Name", editable: true, width: 200 },
                        { field: "finished_norm_annotations", headerName: "Phase1 Finished", type: "number", editable: false, width: 250 },
                        { field: "finished_qa_annotations", headerName: "Phase2 Finished", type: "number", editable: false, width: 250 },
                        { field: "finished_valid_annotations", headerName: "Phase3 Finished", type: "number", editable: false, width: 250 },
                        { field: "p1_task_time", headerName: "P1 Average Time", type: "number", editable: false, width: 220 },
                        { field: "p2_task_time", headerName: "P2 Average Time", type: "number", editable: false, width: 220 },
                        { field: "p3_task_time", headerName: "P3 Average Time", type: "number", editable: false, width: 220 },
                    ],
                    table: user_data
                })


            }).catch((error) => { window.alert(error) })
        } else if (this.props.name == "Claims") {
            this.setState({
                header: [
                    { field: "id", headerName: "ID", width: 120 },
                    { field: "claim_url", editable: true, width: 600 },
                    { field: "phase_1_annotation_ids", editable: true, width: 250 },
                    { field: "phase_2_annotation_ids", editable: true, width: 250 },
                    { field: "phase_3_annotation_ids", editable: true, width: 250 }
                ],
                table: [
                    { id: 0, claim_url: "https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/", phase_1_annotation_ids: [23, 1, 7], phase_2_annotation_ids: [3, 4], phase_3_annotation_ids: [8] },
                ]
            })
        } else if (this.props.name == "Disagreements") {
            this.setState({
                header: [
                    { field: "id", headerName: "ID", width: 120 },
                    {
                        field: "resolve_link",
                        editable: false,
                        width: 250,
                        renderCell: (cellValues) => {
                            return <a href={`/disagreement?id=${cellValues.row.id}`}>Resolve</a>;
                        }
                    },
                    { field: "resolved_status", type: "boolean", editable: true, width: 250 },
                    { field: "phase_2_annotation_id", type: "number", editable: true, width: 250 },
                    { field: "phase_3_annotation_id", type: "number", editable: true, width: 250 }
                ],
                table: [
                    { id: 0, resolved_status: false, phase_2_annotation_id: 3, phase_3_annotation_id: 8 },
                ]
            })
        }
    }

    cellEdit(params, event) {
        console.log(`Editing cell with value: ${params.value} and row id: ${params.id}, column: ${params.field}, triggered by ${event.type}.`)
        // I did not implement code to edit the state here. We should make the API call to edit instead, then reload the entire table. That way, if there is a mistake/lost connection to the server/etc, the state will not falsely update.
    }

    makeNewRow() {
        // This should be an API call
        return { id: this.state.table.length + 1 }
    }

    deleteRows() {
        console.log(`Delete entries by ID: ` + JSON.stringify(this.state.selected))
        // I did not implement code to delete from the state here. We should make the API call to edit instead, then reload the entire table. That way, if there is a mistake/lost connection to the server/etc, the state will not falsely update.
    }

    setSelectedRows(rows) {
        this.setState({
            selected: rows
        })
    }

    addRow() {
        this.setState({
            table: [
                ...this.state.table,
                this.makeNewRow()
            ]
        })
    }

    render() {
        let className = ''

        if (this.props.className !== undefined) {
            className = this.props.className
        }

        let datagrid = ""

        if (this.state.header) {
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

        const availableClaimsData = [
            {
                name: "Unassigned Claims",
                p1: 7500,
                p2: 3500,
                p3: 2500,
                p4: 6500,
                p5: 1500,
            },
        ]

        var hackedDivHeight = 55 * ((this.state.table) ? ((this.state.table.length > 10) ? 10 : this.state.table.length) + 2 : 0) + 10

        return (
            <div className={className}>
                <EntryCard>
                    <Header>{this.props.name}</Header>
                    <div style={{ height: hackedDivHeight, width: '100%' }}>
                        {datagrid}
                        <AddButton variant="contained" color="primary" onClick={this.addRow}>
                            Add Row
                        </AddButton>

                        <JsonButton
                            variant="contained"
                            color="primary"
                            type="button"
                            href={`data:text/json;charset=utf-8,${encodeURIComponent(
                                JSON.stringify(this.state.table)
                            )}`}
                            download={this.props.name + ".json"}
                        >
                            Download as JSON
                        </JsonButton>

                        <DeleteButton variant="contained" color="error" startIcon={<DeleteIcon />} onClick={this.deleteRows}>
                            Delete Selected
                        </DeleteButton>
                    </div>
                </EntryCard>
                {this.props.name === "Users" ?
                    <EntryCard>
                        <Header>Assignments</Header>
                        <AssignChartBox>
                            <BarChart
                                width={100 + availableClaimsData.length * 350}
                                height={300}
                                data={availableClaimsData}
                                margin={{
                                    top: 5,
                                    right: 30,
                                    left: 20,
                                    bottom: 5,
                                }}
                            >
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis dataKey="name" />
                                <YAxis />
                                <Tooltip />
                                <Bar barSize={60} name="Claim Normalization" dataKey="p1" fill="#8884d8" />
                                <Bar barSize={60} name="Question Generation" dataKey="p2" fill="#8dd1e1" />
                                <Bar barSize={60} name="Quality Control" dataKey="p3" fill="#82ca9d" />
                                <Bar barSize={60} name="Dispute Resolution" dataKey="p4" fill="#d0ed57" />
                                <Bar barSize={60} name="Post-Resolution Quality Control" dataKey="p5" fill="#ffc658" />
                            </BarChart>
                        </AssignChartBox>

                        <AssignRadioBox>
                            <FormLabel component="type_legend">Phase:</FormLabel>
                            <RadioGroup aria-label="assignment_phase" name="assignment_phase" defaultValue="1">
                                <FormControlLabel value="1" control={<Radio />} label="Claim Normalization" />
                                <FormControlLabel value="2" control={<Radio />} label="Question Generation" />
                                <FormControlLabel value="3" control={<Radio />} label="Quality Control" />
                                <FormControlLabel value="4" control={<Radio />} label="Dispute Resolution" />
                                <FormControlLabel value="5" control={<Radio />} label="Post-Resolution Quality Control" />
                            </RadioGroup>
                        </AssignRadioBox>
                        
                        <AssignRadioBox>
                            <FormLabel component="type_legend">Assign to:</FormLabel>
                            <RadioGroup aria-label="assignment_type" name="assignment_type" defaultValue="non_admin">
                                <FormControlLabel value="non_admin" control={<Radio />} label="All Non-admins" />
                                <FormControlLabel value="selection" control={<Radio />} label="Selected Users" />
                                <FormControlLabel value="all" control={<Radio />} label="All Users" />
                            </RadioGroup>
                        </AssignRadioBox>

                        <AssignControlTextBox>
                            Assign (up to) <TextField size="small"></TextField> claims to each user. A total of {45} claims will be assigned.
                        </AssignControlTextBox>

                        <AssignControlBox>
                            <AddButton variant="contained" color="secondary">
                                Max
                            </AddButton>
                            <JsonButton variant="contained" color="primary">
                                Assign
                            </JsonButton>
                        </AssignControlBox>


                    </EntryCard>
                    :
                    ""}
            </div>
        );
    }
}

export default AdminControl;
