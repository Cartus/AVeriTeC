import React from 'react';
import { makeStyles } from '@material-ui/styles';
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';
import TooltipQMark from './TooltipQMark';
import styled from 'styled-components';

const useStyles = makeStyles((theme) => ({
  formControl: {
    minWidth: 210,
  },
  selectEmpty: {
  },
}));

const ElementContainer = styled.div`
    margin: 5px;
    width: 280px;
    height: 40px;
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

const StyledFormControl = styled(FormControl)`
    width:100%
`

export default function SelectWithTooltip(props) {
  const classes = useStyles();

  var value = (props.value != null)? props.value : "";

  if (props.validator != null){
    let validation = props.validator(value)
    var error =  validation.error && !props.valid;
    var message = validation.message
  } else{
    var error = false
  }

  const menuItems = props.items.map(item => (
    <MenuItem key={item} value={item}>{item}</MenuItem>
  ));

  return (
    <ElementContainer>
      <TextFieldContainer>
      <StyledFormControl disabled={props.disabled} required={props.required} error={error} variant="outlined" size="small" className={classes.formControl}>
        <InputLabel>{props.label}</InputLabel>
        <Select
          // value={props.value}
          value={value}
          onChange={props.onChange}
          label={props.label}
          name={props.name}
        >
          {menuItems}
        </Select>
        {/*error? <FormHelperText error={error}>{message}</FormHelperText> : ""*/}
      </StyledFormControl>
      </TextFieldContainer>
      <QMarkContainer>
      <TooltipQMark title={props.tooltip}/>
      </QMarkContainer>
    </ElementContainer>
  );
}