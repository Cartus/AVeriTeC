import React from 'react';
import styled from 'styled-components';
import UserPanel from './UserPanel';
import AssignmentControl from './AssignmentControl';
import AdminControl from './AdminControl';
import {Redirect} from "react-router-dom";
import TrainingControl from './TrainingControl';
import PhaseStatsControl from './PhaseStatsControl';
import config from "../config.json"
import axios from "axios";

const AssignmentField = styled(AssignmentControl)`
    width:33.333%;
    float:left;

    @media (max-width: 640px)  {
        width:320px;
    }

    @media (min-width: 640px) and (max-width: 960px)  {
        width:50%;
    }
    
    @media (min-width: 960px)  {
        width:33.333%;
    }
`

const TrainingField = styled(TrainingControl)`
    width:33.333%;
    float:left;

    @media (max-width: 640px)  {
        width:320px;
    }

    @media (min-width: 640px) and (max-width: 960px)  {
        width:50%;
    }
    
    @media (min-width: 960px)  {
        width:33.333%;
    }
`

const AdminPanel = styled.div`
    width: 100%;
    float: left;
`

const SepDiv = styled("div")`
    width:100%;
    float:left;
    height:0px;
`

class AnnotatorControl extends React.Component {
    constructor(props) {
        super(props);
        
        this.state = {
            user : {
                username: localStorage.getItem('user_name'),
                is_admin: Number(localStorage.getItem('is_admin'))
            },
            assignments:{
                phase_1: {
                    done: 0,
                    total: 0
                },
                phase_2: {
                    done: 0,
                    total: 0
                },
                phase_3: {
                    done: 0,
                    total: 0
                },
                phase_4: {
                    done: 0,
                    total: 0
                },
                phase_5: {
                    done: 0,
                    total: 0
                },
                phase_1_training: {
                    done: 0,
                    total: 0
                },
                phase_2_training: {
                    done: 0,
                    total: 0
                },
                phase_3_training: {
                    done: 0,
                    total: 0
                }
            }
        }
      }

    componentDidMount() {
        var request = {
            method: "post",
            baseURL: config.api_url,
            url: "/user_statistics.php",
            data: {
                logged_in_user_id: localStorage.getItem('user_id'),
                req_type: 'get-statistics',
                get_by_user_id: localStorage.getItem('user_id')
            }
        };

        axios(request).then((response) => {
            console.log(response);
            this.setState({
                assignments: {
                    phase_1: {
                        done: response.data.phase_1.annotations_done,
                        total: response.data.phase_1.annotations_assigned
                    },
                    phase_2: {
                        done: response.data.phase_2.annotations_done,
                        total: response.data.phase_2.annotations_assigned
                    },
                    phase_3: {
                        done: response.data.phase_3.annotations_done,
                        total: response.data.phase_3.annotations_assigned
                    },
                    phase_4: {
                        // done: response.data.phase_4.annotations_done,
                        // total: response.data.phase_4.annotations_assigned,
                        done: 0,
                        total: 0
                    },
                    phase_5: {
                        // done: response.data.phase_5.annotations_done,
                        // total: response.data.phase_5.annotations_assigned
                        done: 0,
                        total: 0
                    },
                    phase_1_training: {
                        // done: response.data.phase_1.training_annotations_done,
                        // total: response.data.phase_1.training_annotations_assigned
                        done: 0,
                        total: 0
                    },
                    phase_2_training: {
                        // done: response.data.phase_2.training_annotations_done,
                        // total: response.data.phase_2.training_annotations_assigned
                        done: 0,
                        total: 0
                    },
                    phase_3_training: {
                        // done: response.data.phase_3.training_annotations_done,
                        // total: response.data.phase_3.training_annotations_assigned
                        done: 0,
                        total: 0
                    },
                }
            })

        }).catch((error) => { window.alert(error) });
    }

    render() {
	if (!localStorage.getItem('login')) {
            return <Redirect to='/'/>;
        } 

        return (
            <div>
                <UserPanel user={this.state.user}/>
                <div>
                    {(this.state.assignments.phase_1.total > 0)?<AssignmentField name="Claim Normalization" page="phase_1" assignments={this.state.assignments.phase_1}/>: ""}
                    {(this.state.assignments.phase_2.total > 0)?<AssignmentField name="Question Generation" page="phase_2" assignments={this.state.assignments.phase_2}/>: ""}
                    {(this.state.assignments.phase_3.total > 0)?<AssignmentField name="Quality Control" page="phase_3" assignments={this.state.assignments.phase_3}/>: ""}
                    {(this.state.assignments.phase_4.total > 0)?<AssignmentField name="Question Generation: Round Two" page="phase_4" assignments={this.state.assignments.phase_4}/>: ""}
                    {(this.state.assignments.phase_3.total > 0)?<AssignmentField name="Quality Control: Round Two" page="phase_3" assignments={this.state.assignments.phase_5} add_phase_4_questions={true}/>: ""}
                </div>
                <SepDiv/>
                <div>
                    {(this.state.assignments.phase_1_training.total > 0)?<TrainingField name="Claim Normalization" page="phase_1" assignments={this.state.assignments.phase_1_training}/>: ""}
                    {(this.state.assignments.phase_2_training.total > 0)?<TrainingField name="Question Generation" page="phase_2" assignments={this.state.assignments.phase_2_training}/>: ""}
                    {(this.state.assignments.phase_3_training.total > 0)?<TrainingField name="Quality Control" page="phase_3" assignments={this.state.assignments.phase_3_training}/>: ""}
                </div>
                {(this.state.user.is_admin === 1)? <AdminPanel>
                    <AdminControl name="Users"/> 
                    <PhaseStatsControl phase={1} name={"Analysis | Claim Normalization"}/>
                    <PhaseStatsControl phase={2} name={"Analysis | Question Generation"}/>
                    <PhaseStatsControl phase={3} name={"Analysis | Quality Control"}/>
                </AdminPanel> : ""}
            </div>
        );
      }
}

export default AnnotatorControl;
