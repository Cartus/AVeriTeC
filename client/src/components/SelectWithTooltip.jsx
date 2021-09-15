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
      <FormControl required={props.required} error={error} variant="outlined" size="small" className={classes.formControl}>
        <InputLabel>{props.label}</InputLabel>
        <Select
          value={props.value}
          onChange={props.onChange}
          label={props.label}
          name={props.name}
        >
          {menuItems}
        </Select>
        {error? <FormHelperText error={error}>{message}</FormHelperText> : ""}
      </FormControl>
      </TextFieldContainer>
      <QMarkContainer>
      <TooltipQMark title={props.tooltip}/>
      </QMarkContainer>
    </ElementContainer>
  );
}