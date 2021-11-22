import { maxHeight } from '@mui/system';
import React from 'react';
import Spinner from 'react-spinkit';
import styled from 'styled-components';

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
        loading: true
      };

      
      this.frameHasLoaded = this.frameHasLoaded.bind(this);
    }

  frameHasLoaded = (event) => {
    if (this.props.src && event.target.src === this.props.src){ // Workaround to discard spurious first event
        console.log("Finished loading: " + event.target.src)
        this.setState({
            loading: false
          });
        }
    };

  render() {
    let className = ''

    if(this.props.className !== undefined){
        className = this.props.className
    }

    let iframeStyle = {
        visibility: this.state.loading ? 'hidden' : 'visible',
        height: this.state.loading ? '0px' : '100%'
    }

      return (
        <div className={className}>
          {this.state.loading? <div style={{height: '1px'}}/> : null}
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