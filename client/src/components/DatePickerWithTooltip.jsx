import React from 'react';
import DatePicker from '@material-ui/lab/DatePicker';
import AdapterDateFns from '@material-ui/lab/AdapterDateFns';
import LocalizationProvider from '@material-ui/lab/LocalizationProvider';
import TooltipQMark from './TooltipQMark';
import TextField from '@material-ui/core/TextField';
import styled from 'styled-components';

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

const StyledTextField = styled(TextField)`
    width:100%
`

function shiftPickerDateToUTCDate(pickerDate) {
    const timestamp = Date.UTC(
        pickerDate.getFullYear(),
        pickerDate.getMonth(),
        pickerDate.getDate()
      );
    
    return new Date(timestamp);
}

function shiftFromUTCDate(systemDate) {
    return new Date(
        systemDate.getUTCFullYear(),
        systemDate.getUTCMonth(),
        systemDate.getUTCDate()
      );
}


export default function DatePickerWithTooltip(props) {  
    return (
        <ElementContainer>
            <TextFieldContainer>
            <LocalizationProvider dateAdapter={AdapterDateFns}>
                <DatePicker
                label={props.label}
                value={props.value? shiftFromUTCDate(props.value) : null}
                inputFormat="dd/MM/yyyy"
                onChange={(newValue) => {            
                    // Essentially a massive hack to make datepicker play nice with our onchange function
                    var fakeEvent = new Object();
                    fakeEvent.target = {
                        name: props.name,
                        value: newValue? shiftPickerDateToUTCDate(newValue) : null
                    };
                    
                    props.onChange(fakeEvent)
                }}
                renderInput={(params) => <StyledTextField size="small" {...params} />}
                />
            </LocalizationProvider>
            </TextFieldContainer>
            <QMarkContainer>
            <TooltipQMark title={props.tooltip}/>
            </QMarkContainer>
        </ElementContainer>
    );
  }