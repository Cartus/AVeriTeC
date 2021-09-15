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
    var value = (props.value != null)? props.value : "";

    if (props.validator != null){
        let validation = props.validator(value)
        var error =  validation.error && !props.valid;
        var message = validation.message
      } else{
        var error = false
      }

    return (
        <ElementContainer>
            <TextFieldContainer>
                <TextField size="small" error={error} {...props}/>
                {error? <FormHelperText error={error}>{message}</FormHelperText> : ""}
            </TextFieldContainer>
            <QMarkContainer>
                <TooltipQMark title={props.tooltip}/>
            </QMarkContainer>
        </ElementContainer>
    );
  }