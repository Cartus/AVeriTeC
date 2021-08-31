import React from 'react';
import TextField from '@material-ui/core/TextField';
import TooltipQMark from './TooltipQMark';
import styled from 'styled-components';
import FormHelperText from '@material-ui/core/FormHelperText';


const ElementContainer = styled.div`
    margin: 10px;
    width: 260px;
    height:50px;
`

const TextFieldContainer = styled.div`
    width: 210px;
    float:left;
`

const QMarkContainer = styled.div`
    width:40px;
    padding-left:220px;
    padding-top:7px;
`

export default function TextFieldWithTooltip(props){
    var required_text = ""
    if (props.required != null){
        required_text = <FormHelperText>Required</FormHelperText>
    }

    return (
        <ElementContainer>
            <TextFieldContainer>
                <TextField size="small" {...props}/>
                {required_text}
            </TextFieldContainer>
            <QMarkContainer>
                <TooltipQMark title={props.tooltip}/>
            </QMarkContainer>
        </ElementContainer>
    );
  }