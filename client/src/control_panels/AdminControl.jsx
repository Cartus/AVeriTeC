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
import IconButton from '@material-ui/core/IconButton';
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
import Popup from 'reactjs-popup';
import 'reactjs-popup/dist/index.css';
import md5 from 'md5';
import ClearIcon from '@material-ui/icons/Clear';

const StyledPopup = styled(Popup)` 
    &-content {    
        width:290px!important;
    }
`

const AssignChartBox = styled("div")`
    float: right;
    width: 30%;
    height: 0px;
`

const ModalPartBox = styled("div")`
    width:100%;
    margin:10px;
`

const CancelButton = styled(IconButton)`
    float: right;
    width:40px;
    margin: -10px 0px!important;
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

const generatePassword  = () =>  {
    var length = 8;
    var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var retVal = "";

    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }

    return retVal;
}

const propComparator = (p1, p2) => {
    let p1_n = parseInt(p1.split(" / ")[0])
    let p2_n = parseInt(p2.split(" / ")[0])

    return p1_n - p2_n
}


class AdminControl extends react.Component {
    constructor(props) {
        super(props);

        this.state = {
            selected: [],
            availableClaimsData: [
                {
                    name: "Unassigned Claims",
                    p1: 7500,
                    p2: 3500,
                    p3: 2500,
                    p4: 6500,
                    p5: 1500,
                },
            ],
            assignment:{
                assignment_type: "non_admin",
                assignment_phase: 1
            },
            new_username: ""
        }

        this.cellEdit = this.cellEdit.bind(this);
        this.addRow = this.addRow.bind(this);
        this.addUser = this.addUser.bind(this);
        this.makeNewRow = this.makeNewRow.bind(this);
        this.deleteRows = this.deleteRows.bind(this);
        this.setSelectedRows = this.setSelectedRows.bind(this);
        this.handleAssignFieldChange = this.handleAssignFieldChange.bind(this);
        this.assignMax = this.assignMax.bind(this);
        this.doAssign = this.doAssign.bind(this);
        this.getMax = this.getMax.bind(this);
    }

    getMax(){
        var phase_id = "p1"
        if (this.state.assignment && this.state.assignment.assignment_phase) {
            phase_id = "p" + this.state.assignment.assignment_phase;
        }

        if (this.state.table) {
            var user_count = this.state.table.length // TODO: count non-admins

            if (this.state.assignment && this.state.assignment.assignment_type === "all") {
                user_count = this.state.table.length
            }

            if (this.state.assignment && this.state.assignment.assignment_type === "selection") {
                user_count = this.state.selected? this.state.selected.length : 0
            }

            var total_to_assign = this.state.availableClaimsData[0][phase_id]

            var per_user = 0
            if (user_count > 0){
                per_user = Math.ceil(total_to_assign / user_count)
            }

            return per_user;
        } else {
            return 0;
        }
    }

    assignMax() {
        if (this.state.table) {
            let per_user = this.getMax()

            this.setState(prevState => ({
                assignment: {
                    ...prevState.assignment,
                    n_to_assign: per_user
                }
            }));
        }
    }

    doAssign(){
        var uids = null
        
        if (this.state.assignment.assignment_type != "selection"){
            uids = []
            
            this.state.table.forEach(row => {
                if (this.state.assignment.assignment_type === "all" || !row.is_admin){
                    uids = [
                        ...uids,
                        row.id
                    ]
                }
            })
        }

        var request = {
            method: "post",
            baseURL: config.api_url,
            url: "/assign_claims.php",
            data: {
                user_id: localStorage.getItem('user_id'),
                req_type: this.state.assignment.assignment_phase,
                assignments_per_user: this.state.assignment.n_to_assign,
                assignment_user_ids: this.state.assignment.assignment_type === "selection"? this.state.selected : uids
            }
        };

        axios(request).then((response) => {
            console.log(response.data);
        }).catch((error) => {window.alert(error)})

        // Todo: send axios call, then update state
    }

    handleAssignFieldChange(event) {
        var { name, value } = event.target;

        if (name === "n_to_assign") {
            const re = /^\-?[0-9\b]+$/;
            if (value != '' && !re.test(value)) {
                return;
            }

            value = parseInt(value, 10) - 0

            let max_per_user = this.getMax();            
            value = Math.min(value, max_per_user);
        }

        this.setState(prevState => ({
            assignment: {
                ...prevState.assignment,
                [name]: value
            }
        }), () => {
            if (name != "n_to_assign"){
                let max_per_user = this.getMax();
                if (this.state.assignment.n_to_assign && this.state.assignment.n_to_assign > max_per_user){
                    this.setState(prevState => ({
                        assignment: {
                            ...prevState.assignment,
                            n_to_assign: max_per_user
                        }
                    }));
                }
            }
        });
    }

    loadTableFromDB(){
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

                user_data = user_data.map(user_dict => {
                    let new_dict = user_dict
                    new_dict["finished_norm_annotations_prop"] = new_dict["finished_norm_annotations"] + " / " + 20
                    new_dict["finished_qa_annotations_prop"] = new_dict["finished_qa_annotations"] + " / " + 20
                    new_dict["finished_valid_annotations_prop"] = new_dict["finished_valid_annotations"] + " / " + 20
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
                        { field: "is_admin", headerName: "Admin", editable: true, type: "boolean", width: 150 },
                        { field: "finished_norm_annotations_prop", headerName: "Phase1 Finished", type: "string", editable: false, width: 250, sortComparator: propComparator, align: "right", headerAlign: "right" },
                        { field: "finished_qa_annotations_prop", headerName: "Phase2 Finished", type: "string", editable: false, width: 250, sortComparator: propComparator, align: "right", headerAlign: "right" },
                        { field: "finished_valid_annotations_prop", headerName: "Phase3 Finished", type: "string", editable: false, width: 250, sortComparator: propComparator, align: "right", headerAlign: "right" },
                        { field: "p1_task_time", headerName: "P1 Average Minutes", type: "number", editable: false, width: 220 },
                        { field: "p2_task_time", headerName: "P2 Average Minutes", type: "number", editable: false, width: 220 },
                        { field: "p3_task_time", headerName: "P3 Average Minutes", type: "number", editable: false, width: 220 },
                        { field: "total_hours", headerName: "Total Hours Worked", type: "number", editable: false, width: 220 },
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

    componentDidMount() {
        this.loadTableFromDB()
    }

    cellEdit(params, event) {
        console.log(`Editing cell with row id: ${params.id} and column: ${params.field} to have value: ${params.value}, triggered by ${event.type}.`)

                
        if (this.props.name == "Users") {
            var request = {
                method: "post",
                baseURL: config.api_url,
                url: "/admin_control.php",
                data: {
                    user_id: localStorage.getItem('user_id'),
                    req_type: 'edit-users',
                    user_id_to_edit: params.id,
                    [params.field]: params.value // this will automatically become e.g. user_name: EditedMichael
                }
            };

            axios(request).then((response) => {
                console.log(response.data);
                this.loadTableFromDB();
            }).catch((error) => {window.alert(error)})
        }
    }

    makeNewRow() {
        // This should be an API call creating a new entry in the approppriate table. Then, we should reload the entire table. That way, if there is a mistake/lost connection to the server/etc, the state will not falsely update.
        // We don't use this since the only table we render is users, so please ignore
        return { id: this.state.table.length + 1 }
    }

    deleteRows() {
        console.log(`Delete entries by ID: ` + JSON.stringify(this.state.selected))

        if (this.props.name == "Users") {
            var request = {
                method: "post",
                baseURL: config.api_url,
                url: "/admin_control.php",
                data: {
                    user_id: localStorage.getItem('user_id'),
                    req_type: 'remove-users',
                    user_ids_to_delete: this.state.selected
                }
            };

            axios(request).then((response) => {
                console.log(response.data);
                this.loadTableFromDB();
            }).catch((error) => {window.alert(error)})
        }
    }

    setSelectedRows(rows) {
        this.setState({
            selected: rows
        }, () => {
            if (this.props.name === "Users" && this.state.assignment.assignment_type === "selection"){
                let max_per_user = this.getMax();
                if (this.state.assignment.n_to_assign > max_per_user){
                    this.setState(prevState => ({
                        assignment: {
                            ...prevState.assignment,
                            n_to_assign: max_per_user
                        }
                    }));
                }
            }
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

    addUser() {
        let username = this.state.new_username;
        let password = this.state.tempPassword;
        let password_md5 = md5(password);

        // Create user
        // Warning: I copypasted this code from registration
        var request = {
            method: "post",
            baseURL: config.api_url,
            url: "/registration.php",
            data:{
              name: username,
              password: password,
              password_md5: password_md5
            }
          };

          axios(request).then((response) => {
            console.log(response.data);
            this.loadTableFromDB();
          }).catch((error) => {window.alert(error)})	

        this.setState({new_username: ""})
        this.setState({tempPassword: ""})
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
                onCellEditCommit={this.cellEdit}
                onSelectionModelChange={this.setSelectedRows}
            />
        }

        var assign_count = 0

        var phase_id = "p1"
        if (this.state.assignment && this.state.assignment.assignment_phase) {
            phase_id = "p" + this.state.assignment.assignment_phase;
        }

        if (this.state.table) {
            var user_count = this.state.table.length // TODO: count non-admins

            if (this.state.assignment && this.state.assignment.assignment_type === "all") {
                user_count = this.state.table.length
            }

            if (this.state.assignment && this.state.assignment.assignment_type === "selection") {
                user_count = this.state.selected.length
            }

            if (this.state.assignment && this.state.assignment.n_to_assign) {
                assign_count = Math.min(this.state.availableClaimsData[0][phase_id], this.state.assignment.n_to_assign * user_count);
            }
        }

        var hackedDivHeight = 55 * ((this.state.table) ? ((this.state.table.length > 10) ? 10 : this.state.table.length) + 2 : 0) + 10

        return (
            <div className={className}>
                <EntryCard>
                    <Header>{this.props.name}</Header>
                    <div style={{ height: hackedDivHeight, width: '100%' }}>
                        {datagrid}
                        {this.props.name === "Users"? 
                        <StyledPopup trigger={
                            <AddButton variant="contained" color="primary">
                                Create User
                            </AddButton>
                            } 
                            onOpen={() => {
                                let password = generatePassword()
                                console.log(password)
                                this.setState({tempPassword: password})
                                }}
                        modal
                        >
                            {(close) => 
                            <div>
                                <CancelButton onClick={close}><ClearIcon /></CancelButton>
                                 <ModalPartBox>
                                Create a new user. Username:
                                </ModalPartBox>
                                <ModalPartBox>
                                <TextField value={this.state.new_username} size="small" name="new_username" onChange={(event) => {
                                    this.setState({new_username: event.target.value})
                                }}></TextField>
                                </ModalPartBox>
                                <ModalPartBox>
                                Temporary password:
                                </ModalPartBox>
                                <ModalPartBox>
                                <TextField value={this.state.tempPassword? this.state.tempPassword : ""} size="small" InputProps={{readOnly: true}} variant="filled" name="new_password"></TextField>
                                </ModalPartBox>
                                <ModalPartBox>
                                <AddButton variant="contained" color="primary" onClick={() => {this.addUser(); close();}}>
                                    Create User
                                </AddButton>
                                </ModalPartBox>
                            </div>}
                        </StyledPopup> 
                        : 
                        <AddButton variant="contained" color="primary" onClick={this.addRow}>
                            Create Row
                        </AddButton>
                        }
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
                                width={100 + this.state.availableClaimsData.length * 350}
                                height={300}
                                data={this.state.availableClaimsData}
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
                            <RadioGroup onChange={this.handleAssignFieldChange} aria-label="assignment_phase" name="assignment_phase" value={this.state.assignment && this.state.assignment.assignment_phase ? this.state.assignment.assignment_phase : 1}>
                                <FormControlLabel value="1" control={<Radio />} label="Claim Normalization" />
                                <FormControlLabel value="2" control={<Radio />} label="Question Generation" />
                                <FormControlLabel value="3" control={<Radio />} label="Quality Control" />
                                <FormControlLabel value="4" control={<Radio />} label="Dispute Resolution" />
                                <FormControlLabel value="5" control={<Radio />} label="Post-Resolution Quality Control" />
                            </RadioGroup>
                        </AssignRadioBox>

                        <AssignRadioBox>
                            <FormLabel component="type_legend">Assign to:</FormLabel>
                            <RadioGroup onChange={this.handleAssignFieldChange} aria-label="assignment_type" name="assignment_type" value={this.state.assignment && this.state.assignment.assignment_type ? this.state.assignment.assignment_type : "non_admin"}>
                                <FormControlLabel value="non_admin" control={<Radio />} label="All Non-admins" />
                                <FormControlLabel value="selection" control={<Radio />} label="Selected Users" />
                                <FormControlLabel value="all" control={<Radio />} label="All Users" />
                            </RadioGroup>
                        </AssignRadioBox>

                        <AssignControlTextBox>
                            Assign (up to) <TextField value={this.state.assignment && (this.state.assignment.n_to_assign || this.state.assignment.n_to_assign === 0)? this.state.assignment.n_to_assign : ""} size="small" name="n_to_assign" type="number" onChange={this.handleAssignFieldChange}></TextField> claims to each user.
                            {this.state.table ? " A total of " + assign_count + " claims will be assigned." : ""}
                        </AssignControlTextBox>

                        <AssignControlBox>
                            <AddButton variant="contained" onClick={this.assignMax} color="secondary">
                                Max
                            </AddButton>
                            {this.state.assignment.n_to_assign && !(this.state.assignment.assignment_type === "selection" && this.state.selected.length === 0)?
                            <JsonButton variant="contained" onClick={this.doAssign} color="primary">
                                Assign
                            </JsonButton>
                            :
                            <JsonButton variant="contained" disabled color="primary">
                                Assign
                            </JsonButton>
                            }
                        </AssignControlBox>
                    </EntryCard>
                    :
                    ""}
            </div>
        );
    }
}

export default AdminControl;
