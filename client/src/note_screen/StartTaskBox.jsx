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

class StartTaskBox extends React.Component {

    constructor(props) {
        super(props);

    }

    render() {

        return (
            <EntryCard>
                <TaskDescriptionBox>
                    {this.props.children}
                </TaskDescriptionBox>

                {this.props.resume ?
                    <StartButton variant="contained" color="primary" onClick={(e) => {
                        e.preventDefault();
                        window.location.href = this.props.taskLink;
                    }}>
                        Resume Task
                    </StartButton>
                    :
                    <StartButton variant="contained" color="primary" onClick={(e) => {
                        e.preventDefault();
                        window.location.href = this.props.taskLink;
                    }}>
                        Begin Task
                    </StartButton>
                }
            </EntryCard>
        );
    }
}

export default StartTaskBox