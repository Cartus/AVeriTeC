import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import Button from '@material-ui/core/Button';

const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px 5px 20px;
`

const Header = styled.h4`
`

const StartButton = styled(Button)`
float:left;
width:120px;
margin:10px 0px!important;
`


class AssignmentControl extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        var assignments = this.props.assignments.map((assignment) => {
            var link_str = "/" + this.props.page + "?claim_id=" + assignment;
            return <li><a href={link_str}>{assignment}</a></li>
        });

        if (this.props.assignments.length > 0){
            var firstAssignmentLink = "/" + this.props.page + "?claim_id=" + this.props.assignments[0]
        }

        return (
            <div className={className}>
                <EntryCard>
                    <Header>Assignments: {this.props.name}</Header>
                    <ul>
                        {assignments}
                    </ul>         
                    {(this.props.assignments.length > 0)?                
                    <StartButton variant="contained" color="primary" onClick={(e) => {
                        e.preventDefault();
                        window.location.href=firstAssignmentLink;
                        }}>
                        Start Next
                    </StartButton>
                    : ""
                    }
                </EntryCard> 
            </div>
        );
    }
}

export default AssignmentControl;