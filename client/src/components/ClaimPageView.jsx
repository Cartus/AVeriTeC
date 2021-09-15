import React from 'react';
import styled from 'styled-components';

const ClaimFrame = styled.iframe`
    width: 99.7%;
    height: 70vh;
`

class ClaimPageView extends React.Component {
    constructor(props) {
        super(props);
      }

    render() {

        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        return (
            <div style={this.props.style} className={className}>
                <ClaimFrame src={this.props.claim.web_archive}/>
            </div>
            
        );
      }
}

export default ClaimPageView;