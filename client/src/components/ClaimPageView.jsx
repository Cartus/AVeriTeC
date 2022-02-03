import React from 'react';
import styled from 'styled-components';
import LoadingIFrame from './LoadingIFrame';

const ClaimFrame = styled(LoadingIFrame)`
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
            <div data-tour="claim_page_view" style={this.props.style} className={className}>
                <ClaimFrame src={this.props.claim.web_archive} phase={this.props.phase}/>
            </div>
            
        );
      }
}

export default ClaimPageView;