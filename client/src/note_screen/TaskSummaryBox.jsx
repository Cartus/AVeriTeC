import React from 'react';
import styled from 'styled-components';
import Card from '@material-ui/core/Card';
import { Button } from '@material-ui/core';

const EntryCard = styled(Card)`
  margin:10px;
  padding:10px;
`

const StartButton = styled(Button)`
  float:right;
  width:160px;
`

const TaskDescriptionBox = styled("div")`
  width:100%
`

class TaskSummaryBox extends React.Component {

    constructor(props) {
        super(props);

    }

    render() {

        return (
            <EntryCard>
                <TaskDescriptionBox>
                    {this.props.children}
                </TaskDescriptionBox>

                {this.props.continue ?
                    <StartButton variant="contained" color="primary" onClick={(e) => {
                        e.preventDefault();
                        window.location.href = this.props.taskLink;
                    }}>
                        Continue
                    </StartButton>
                    :
                    ""
                }
            </EntryCard>
        );
    }
}

export default TaskSummaryBox