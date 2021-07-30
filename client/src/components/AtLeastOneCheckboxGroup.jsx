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
`

const QMarkContainer = styled.div`
    width:40px;
    padding-top:7px;
    display:inline;
`

export default function AtLeastOneCheckboxGroup(props) {
  const classes = useStyles();

  const optionDict = {};
  const tooltipDict = {}
  props.items.forEach(option => {
    optionDict[option["label"]] = false;
    tooltipDict[option["label"]] = option["tooltip"];
  });
  const [state, setState] = React.useState(optionDict);

  const handleChange = (event) => {
    const updState = { ...state, [event.target.name]: event.target.checked};
    setState(updState);

    // TODO: Ugly hack to mutate data, please fix
    var fakeEvent = new Object();
    fakeEvent.target = {
      name: props.name,
      value: Object.keys(state).filter(v => updState[v])
    };
    props.onChange(fakeEvent)
  };

  const error = Object.keys(state).filter(v => state[v]).length === 0;

  const items = Object.keys(state).map(v => (
    <div>
        <FormControlLabel
        control={<Checkbox checked={state[v]} 
        onChange={handleChange} 
        name={v} />}
        label={v}
        />
      <TooltipQMark title={tooltipDict[v]} discrete/>
    </div>
  ));

  return (
    
    <div className={classes.root}>
      <FormControl required error={error} component="fieldset" className={classes.formControl}>
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
        <FormHelperText>Pick at least one</FormHelperText>
      </FormControl>
    </div>
  );
}