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
        return (
            <ClaimFrame src={this.props.claim.web_archive} style={this.props.style}/>
        );
      }
}

export default ClaimPageView;