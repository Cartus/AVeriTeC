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
  const [item, setItem] = React.useState('');

  const handleChange = (event) => {
    setItem(event.target.value);
    props.onChange(event)
  };

  const menuItems = props.items.map(item => (
    <MenuItem key={item} value={item}>{item}</MenuItem>
  ));

  return (
    <ElementContainer>
      <TextFieldContainer>
      <FormControl required variant="outlined" size="small" className={classes.formControl}>
        <InputLabel>{props.label}</InputLabel>
        <Select
          value={item}
          onChange={handleChange}
          label={props.label}
          name={props.name}
        >
          {menuItems}
        </Select>
        <FormHelperText>Required</FormHelperText>
      </FormControl>
      </TextFieldContainer>
      <QMarkContainer>
      <TooltipQMark title={props.tooltip}/>
      </QMarkContainer>
    </ElementContainer>
  );
}