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
float:right;
width:120px;
margin:10px 0px!important;
`

const CountBox = styled.div`
float:left;
width:200px;
margin:30px 0px 0px 0px!important;
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

        var assignments = "Finished " + this.props.assignments.done + "/" + this.props.assignments.total + "."

        var nextAssignmentLink = "/" + this.props.page + "/begin"

        return (
            <div className={className}>
                <EntryCard>
                    <Header>Assignments: {this.props.name}</Header>
                    <CountBox>{assignments}</CountBox>

                    {this.props.assignments.done != this.props.assignments.total?
                    <StartButton variant="contained" color="primary" onClick={(e) => {
                        e.preventDefault();
                        localStorage.setItem('phase', this.props.page);
                        window.location.href=nextAssignmentLink;
                        }}>
                        To Task
                    </StartButton>
                    :
                    <StartButton variant="contained" color="primary" disabled>
                        To Task
                    </StartButton>                    
                    }
                </EntryCard> 
            </div>
        );
    }
}

export default AssignmentControl;