import React from 'react';
import styled from 'styled-components';

const ClaimFrame = styled.iframe`
    width: 99.7%;
    height:100%;
    border:none;
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
                <ClaimFrame data-tour="claim_page_view" src={this.props.claim.web_archive}/>
            </div>
            
        );
      }
}

export default ClaimPageView;