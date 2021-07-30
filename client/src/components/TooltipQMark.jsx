import React from 'react';
import styled from 'styled-components';
import Tooltip from '@material-ui/core/Tooltip';

const QMark = styled.span`
    position: relative;
    background: rgba(0,0,0,0.3);
    padding: 5px 11px;
    border-radius: 50%;
    font-size: 20px;
    cursor: help;
    text-align: center;
    font-size: 14px;
    height: 25px;
    width: 25px;
`

const DiscreteQMark = styled.span`
    position: relative;
    padding: 5px 11px;
    border-radius: 50%;
    font-size: 20px;
    cursor: help;
    text-align: center;
    font-size: 14px;
    height: 25px;
    width: 25px;
`

class TooltipQMark extends React.Component {
    constructor(props) {
        super(props);
      }

    render() {
        if (this.props.discrete != null){
            return (
                <Tooltip title={this.props.title} arrow>
                    <DiscreteQMark>(?)</DiscreteQMark>
                </Tooltip>
            );
        } else {
            return (
                <Tooltip title={this.props.title} arrow>
                    <QMark>?</QMark>
                </Tooltip>
            );
        }
      }
}

export default TooltipQMark;