import React from 'react';
import AppBar from '@mui/material/AppBar';
import Toolbar from '@mui/material/Toolbar';
import Typography from '@mui/material/Typography';
import styled from 'styled-components';
import MetadataEntryBar from '../claim_normalization/MetadataEntryBar';
import axios from "axios";
import config from "../config.json"
import Card from '@material-ui/core/Card';
import moment from "moment";
import QuestionGenerationBar from "../question_generation/QuestionGenerationBar"
import VerdictValidationBar from '../verdict_validation/VerdictValidationBar';
import IconButton from '@material-ui/core/IconButton';
import NavigateNextIcon from '@material-ui/icons/NavigateNext';
import NavigateBeforeIcon from '@material-ui/icons/NavigateBefore';
import StartTaskBox from './StartTaskBox';
import TaskSummaryBox from './TaskSummaryBox';

const NoteView = styled("div")`
    width: 100%
    height: -webkit-calc(100vh - 68px)!important;
    height:    -moz-calc(100vh - 68px)!important;
    height:         calc(100vh - 68px)!important;
    overflow: auto;
`

const LogoutBox = styled.div`
    float:right;
    margin: 10px;
    text-colour:white;
`

const WhiteLink = styled.a`
  color:white;
`

const BarPartBox = styled("div")`
  width: -webkit-calc(100% - 270px)!important;
  width:    -moz-calc(100% - 270px)!important;
  width:         calc(100% - 270px)!important;
  float:left;
`

const PaddingTypographBox = styled(Typography)`
  padding: 10px 18px;
  width: 100%;
  float:left;
  text-align:left;
`

const ShoveBox = styled("div")`
  width: 1px;
  height: 64px;
`

class NoteScreen extends React.Component {

    constructor(props) {
        super(props);

    }

    render() {

        return (
            <div>
                <AppBar>
                    <Toolbar>
                        <BarPartBox>
                            <PaddingTypographBox variant="h6" component="div">
                                {this.props.header}
                            </PaddingTypographBox>
                        </BarPartBox>
                        <LogoutBox>
                        <WhiteLink href={config.api_url + "/guideline.pdf"} >Guidelines</WhiteLink> | <WhiteLink href="/control" >Control Panel</WhiteLink> | <WhiteLink href="#" onClick={() => {}}>Log out</WhiteLink>
                        </LogoutBox>
                    </Toolbar>
                </AppBar>
                <ShoveBox />

                <NoteView>
                    {this.props.children}
                </NoteView>
            </div>
        );
    }
}

export default NoteScreen