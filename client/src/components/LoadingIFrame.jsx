import { maxHeight } from '@mui/system';
import React from 'react';
import Spinner from 'react-spinkit';
import styled from 'styled-components';
import config from "../config.json"
import axios from 'axios';

const FullFrame = styled.iframe`
    width:100%;
    border:none;
`

const StyledSpinner = styled(Spinner)`
    width:80px;
    height:80px;
    border: 1px solid transparent;
    display: block;

    margin: -webkit-calc(50vh - 60px) 0px 0px -webkit-calc(50% - 40px)!important;
    margin: -moz-calc(50vh - 60px) 0px 0px -moz-calc(50% - 40px)!important;
    margin: calc(50vh - 60px) 0px 0px calc(50% - 40px)!important;
`

class LoadingIFrame extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loading: true,
      loading_start_time: new Date()
    };


    this.frameHasLoaded = this.frameHasLoaded.bind(this);
  }

  frameHasLoaded = (event) => {
    if (this.props.src && event.target.src === this.props.src) { // Workaround to discard spurious first event
      var loading_start_time = this.state.loading_start_time;
      var loading_stop_time = new Date();

      var seconds_taken = (loading_stop_time.getTime() - loading_start_time.getTime()) / 1000;

      console.log("Finished loading: " + event.target.src)
      console.log("Phase: " + this.props.phase)
      console.log(
        "Loading started at " + loading_start_time + " and finished at " + loading_stop_time + ". Loading took " + seconds_taken + " seconds."
      )
      this.setState({
        loading: false
      });
    }

    // let phase = localStorage.getItem('phase');
    var request = {
      method: "post",
      baseURL: config.api_url,
      url: "/finished_loading.php",
      data: {
        user_id: localStorage.getItem('user_id'),
        claim_id: localStorage.claim_id,
        claim_norm_id: localStorage.claim_norm_id,
        // loading_start_time: loading_start_time,
        // loading_stop_time: loading_stop_time,
        // seconds_taken: seconds_taken,
        phase: this.props.phase
      }
    };

    axios(request).then((response) => {
      console.log(response.data);
    }).catch((error) => { console.log(error) })
  };

  render() {
    let className = ''

    if (this.props.className !== undefined) {
      className = this.props.className
    }

    let iframeStyle = {
      visibility: this.state.loading ? 'hidden' : 'visible',
      height: this.state.loading ? '0px' : '100%'
    }

    return (
      <div className={className}>
        {this.state.loading ? <div style={{ height: '1px' }} /> : null}
        {this.state.loading ? (
          <StyledSpinner
            name="circle"
          />
        ) : null}
        <FullFrame
          style={iframeStyle}
          src={this.props.src}
          onLoad={this.frameHasLoaded}
        />
      </div>
    );
  }
}

export default LoadingIFrame;