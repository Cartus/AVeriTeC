import React from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import Button from '@material-ui/core/Button';
import axios from "axios";
import {Redirect} from "react-router-dom";
import config from "../config.json";

const EntryCard = styled(Card)`
  margin:10px;
`

const LogoutBox = styled.div`
    float:right;
    margin: 10px;
`

const ReportBox = styled.div`
    width: -webkit-calc(100% - 20px);
    width:    -moz-calc(100% - 20px);
    width:         calc(100% - 20px);
    float:left;
    padding: 10px 0px 0px 0px;
`

const PhaseHeader = styled.h4`
    margin: 10px 10px;
    float: left;
`

const PhaseDescriptionBox = styled.div`
    width: -webkit-calc(100% - 20px);
    width:    -moz-calc(100% - 20px);
    width:         calc(100% - 20px);
    float:left;
    padding:10px;
`

class PhaseControl extends React.Component {
    constructor(props) {
        super(props);
	
	this.state = {
            login: true
        }    
        this.onLogout = this.onLogout.bind(this)
        this.onReport = this.onReport.bind(this)
    }

    onLogout(e){
        e.preventDefault();
        localStorage.clear();
	this.setState({login: false});
    };

    async onReport() {
        let phase = localStorage.getItem('phase');
        if (phase === 'phase_1') {
	     var request = {
                method: "post",
                baseURL: config.api_url,
                url: "/claim_norm.php",
                data:{
                    user_id: localStorage.getItem('user_id'),
                    req_type: 'skip-data',
                    claim_id: localStorage.claim_id
                }
            };

            await axios(request).then((response) => {
                console.log(response.data);
		        // localStorage.finished_norm_annotations = Number(localStorage.finished_norm_annotations) + 1;
                window.location.reload(false);
            }).catch((error) => {window.alert(error)})	
        } else if (phase === 'phase_2') {
	    var request = {
                method: "post",
                baseURL: config.api_url,
                url: "/question_answering.php",
                data:{
                    user_id: localStorage.getItem('user_id'),
                    req_type: 'skip-data',
                    claim_id: localStorage.claim_id
                }
            };

            await axios(request).then((response) => {
                console.log(response.data);
		        // localStorage.finished_qa_annotations = Number(localStorage.finished_qa_annotations) + 1;
                window.location.reload(false);
            }).catch((error) => {window.alert(error)})	
        }
    };

    render() {
	if (!this.state.login) {
            return <Redirect to='/'/>;
        }

        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        var current_idx = "?"
        if (this.props.current_idx || this.props.current_idx == 0){
            current_idx = this.props.current_idx
        }

        var final_idx = "?"
        if (this.props.final_idx || this.props.final_idx == 0){
            final_idx = this.props.final_idx
        }

        return (
            <EntryCard className={className}>
                <PhaseHeader>{this.props.phaseName} ({current_idx}/{final_idx})</PhaseHeader>
                <LogoutBox>
                    <a target="_blank" rel="noopener noreferrer" href={config.api_url + "/guideline.pdf"} >Guidelines</a> | <a href="/control" >Control Panel</a> | <a href="#" onClick={this.onLogout}>Log out</a>
                </LogoutBox>
                <PhaseDescriptionBox>
                    {this.props.phaseInstructions}
                    {this.props.reportButton? 
                    <ReportBox>
                        If the fact checking article displays a 404 page or another error, or if it takes more than one minute to load, please <a href="#" data-tour="report" onClick={this.onReport}>let us know</a>.
                    </ReportBox> : ""}
                </PhaseDescriptionBox>
            </EntryCard>
        );
    }
}

export default PhaseControl;
