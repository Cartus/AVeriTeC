import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';

const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px 5px 20px;
`

const Header = styled.h4`
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

        return (
            <div className={className}>
                <EntryCard>
                    <Header>Assignments: {this.props.name}</Header>
                    <ul>
                        {assignments}
                    </ul>
                </EntryCard> 
            </div>
        );
    }
}

export default AssignmentControl;