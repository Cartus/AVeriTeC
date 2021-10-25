import React from 'react';
import styled from 'styled-components';
import UserPanel from './UserPanel';
import AssignmentControl from './AssignmentControl';
import AdminControl from './AdminControl';

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

const AdminPanel = styled.div`
    width: 100%;
    float: left;
`

class AnnotatorControl extends React.Component {
    constructor(props) {
        super(props);
        
        this.state = {
            user : {
                username: "Michael",
                is_admin: true
            },
            assignments:{
                phase_1: {
                    done: 5,
                    total: 10
                },
                phase_2: {
                    done: 3,
                    total: 10
                },
                phase_3: {
                    done: 5,
                    total: 7
                }
            }
        }
      }

    render() {
        return (
            <div>
                <UserPanel user={this.state.user}/>
                <div>
                    {(this.state.assignments.phase_1.total - this.state.assignments.phase_1.done > 0)?<AssignmentField name="Claim Normalization" page="phase_1" assignments={this.state.assignments.phase_1}/>: ""}
                    {(this.state.assignments.phase_2.total - this.state.assignments.phase_2.done > 0)?<AssignmentField name="Question Generation" page="phase_2" assignments={this.state.assignments.phase_2}/>: ""}
                    {(this.state.assignments.phase_3.total - this.state.assignments.phase_3.done > 0)?<AssignmentField name="Quality Control" page="phase_3" assignments={this.state.assignments.phase_3}/>: ""}
                </div>
                {(this.state.user.is_admin)? <AdminPanel>
                    <AdminControl name="Users"/>
                    <AdminControl name="Claims"/>
                    <AdminControl name="Potential other table"/>
                </AdminPanel> : ""}
            </div>
        );
      }
}

export default AnnotatorControl;