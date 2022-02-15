import React from 'react';
import styled from 'styled-components';
import UserPanel from './UserPanel';
import AssignmentControl from './AssignmentControl';
import AdminControl from './AdminControl';
import {Redirect} from "react-router-dom";
import TrainingControl from './TrainingControl';
import PhaseStatsControl from './PhaseStatsControl';

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
                    done: localStorage.finished_norm_annotations,
                    total: 20
                },
                phase_2: {
                    done: localStorage.finished_qa_annotations,
                    total: 20
                },
                phase_3: {
                    done: localStorage.finished_valid_annotations,
                    total: 20
                },
                phase_1_training: {
                    done: 0,
                    total: 10
                },
                phase_2_training: {
                    done: 2,
                    total: 10
                },
                phase_3_training: {
                    done: 0,
                    total: 10
                }
            }
        }
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
                </div>
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
