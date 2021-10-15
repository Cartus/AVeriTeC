import React from 'react';
import TextField from '@material-ui/core/TextField';
import TooltipQMark from './TooltipQMark';
import styled from 'styled-components';
import FormHelperText from '@material-ui/core/FormHelperText';
import { areDayPropsEqual } from '@material-ui/lab/PickersDay/PickersDay';


const ElementContainer = styled.div`
    margin: 6px;
    width: 280px;
    height: ${(props) => {
        var space = 0;
        if (props.multiline){
            if (props.rows == 3){
                space = 98
            } else{
                space += 40 * (~~(props.rows/2) + 1);
                space += 6 * ~~(props.rows/2);
            }
        } else{
            space = 40;
        }
        return space + "px";
    }};
`

const TextFieldContainer = styled.div`
    width: -webkit-calc(100% - 47px)!important;
    width:    -moz-calc(100% - 47px)!important;
    width:         calc(100% - 47px)!important;
    float:left;
`

const QMarkContainer = styled.div`
    width:40px;
    float:right;
    padding-top:7px;
`

const StyledTextField = styled(TextField)`
    width:100%
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
        <ElementContainer multiline={props.rows} rows={props.rows}>
            <TextFieldContainer>
                <StyledTextField size="small" error={error} {...props}/>
                {/*error? <FormHelperText error={error}>{message}</FormHelperText> : ""*/}
            </TextFieldContainer>
            <QMarkContainer>
                <TooltipQMark title={props.tooltip}/>
            </QMarkContainer>
        </ElementContainer>
    );
  }