import React from 'react';
import { makeStyles } from '@material-ui/styles';
import InputLabel from '@material-ui/core/InputLabel';
import MenuItem from '@material-ui/core/MenuItem';
import FormHelperText from '@material-ui/core/FormHelperText';
import FormControl from '@material-ui/core/FormControl';
import Select from '@material-ui/core/Select';
import TooltipQMark from './TooltipQMark';
import styled from 'styled-components';
import Autocomplete from '@mui/material/Autocomplete';
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import countryFlagEmoji from "country-flag-emoji";

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

const StyledTextField = styled(TextField)`
    width:100%
`

export default function CountryPickerWithTooltip(props) {
  const classes = useStyles();

  var value = (props.value != null) ? props.value : "";

  if (props.validator != null) {
    let validation = props.validator(value)
    var error = validation.error && !props.valid;
    var message = validation.message
  } else {
    var error = false
  }

  var country_list = countryFlagEmoji.list.filter((entry) => { return !["European Union", "United Nations"].includes(entry.name) }).sort((a, b) => {
    var textA = a.name.toUpperCase();
    var textB = b.name.toUpperCase();
    return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
  });

  var country_by_code_dict = country_list.reduce((a, x) => ({ ...a, [x.code]: x }), {})
  // console.log(country_by_code_dict[props.value])

  const handleChange = (event, values) => {
    // TODO: Ugly hack to mutate data, please fix
    var fakeEvent = new Object();
    fakeEvent.target = {
      name: props.name,
      value: values.code
    };
    props.onChange(fakeEvent)
  };


  return (
    <ElementContainer>
      <TextFieldContainer>
        {props.readOnly ?
          <StyledTextField size="small" inputProps={{ readOnly: true }} {...props} value={country_by_code_dict[props.value]? country_by_code_dict[props.value].name  + " (" + country_by_code_dict[props.value].code + ")": ""} variant="filled" />
          :
          <Autocomplete
            value={country_by_code_dict[props.value]}
            options={country_list}
            onChange={handleChange}
            autoHighlight
            getOptionLabel={(option) => option.name + " (" + option.code + ")"}
            renderOption={(props, option) => (
              <Box component="li" sx={{ '& > img': { mr: 2, flexShrink: 0 } }} {...props}>
                {option.emoji}  {option.name} ({option.code})
              </Box>
            )}
            renderInput={(params) => (
              <TextField
                {...params}
                name={props.name}
                size="small"
                label={props.label}
                inputProps={{
                  ...params.inputProps,
                  autoComplete: 'new-password', // disable autocomplete and autofill
                }}
              />
            )}
          />
        }
        {/*error? <FormHelperText error={error}>{message}</FormHelperText> : ""*/}
      </TextFieldContainer>
      <QMarkContainer>
        <TooltipQMark title={props.tooltip} />
      </QMarkContainer>
    </ElementContainer>
  );
}