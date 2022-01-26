import React from 'react';
import { makeStyles } from '@material-ui/styles';
import FormLabel from '@material-ui/core/FormLabel';
import FormControl from '@material-ui/core/FormControl';
import FormGroup from '@material-ui/core/FormGroup';
import FormControlLabel from '@material-ui/core/FormControlLabel';
import FormHelperText from '@material-ui/core/FormHelperText';
import Checkbox from '@material-ui/core/Checkbox';
import TooltipQMark from './TooltipQMark';
import Tooltip from '@material-ui/core/Tooltip';
import styled from 'styled-components';

const useStyles = makeStyles((theme) => ({
  root: {
    display: 'flex',
  }
}));

const LabelContainer = styled.div`
    float:left;
    padding-right:20px;
    padding-bottom:7px;
`

const QMarkContainer = styled.div`
    width:40px;
    float:left;
    height:35px;
`


export default function AtLeastOneCheckboxGroup(props) {
  const classes = useStyles();

  var data = (props.data != null)? props.data : [];

  const optionDict = {};
  const tooltipDict = {}
  props.items.forEach(option => {
    optionDict[option["label"]] = (props.data != null)? props.data.includes(option["label"]): false;
    tooltipDict[option["label"]] = option["tooltip"];
  });

  const handleChange = (event) => {
    optionDict[event.target.name] = event.target.checked;

    // TODO: Ugly hack to mutate data, please fix
    var fakeEvent = new Object();
    fakeEvent.target = {
      name: props.name,
      value: Object.keys(optionDict).filter(v => optionDict[v])
    };
    props.onChange(fakeEvent)
  };

  if (props.validator != null){
    let validation = props.validator(data)
    var error =  validation.error && !props.valid;
    var message = validation.message
  } else{
    var error = false
  }

  //let error = Object.keys(optionDict).filter(v => optionDict[v]).length === 0 
  //props.validationListener(props.name, !error)

  const items = Object.keys(optionDict).map(v => (
    <div>
        <FormControlLabel
        control={<Checkbox checked={optionDict[v]} 
        onChange={handleChange} 
        name={v} />}
        label={v}
        disabled={props.readOnly}
        />
      <TooltipQMark title={tooltipDict[v]} discrete/>
    </div>
  ));

  return (
    <div className={classes.root}>
      <FormControl required={props.required} error={error} component="fieldset" className={classes.formControl}>
        <div>
          <LabelContainer>
          <FormLabel component="legend">{props.label}</FormLabel>
          </LabelContainer>
          <QMarkContainer>
          <TooltipQMark title={props.tooltip}/>
          </QMarkContainer>
        </div>
        <FormGroup>
          {items}
        </FormGroup>
        {error? <FormHelperText>{message}</FormHelperText> : ""}
      </FormControl>
    </div>
  );
}