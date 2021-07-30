import React from 'react';
import DatePicker from '@material-ui/lab/DatePicker';
import AdapterDateFns from '@material-ui/lab/AdapterDateFns';
import LocalizationProvider from '@material-ui/lab/LocalizationProvider';
import TooltipQMark from './TooltipQMark';
import TextField from '@material-ui/core/TextField';
import styled from 'styled-components';

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

export default function DatePickerWithTooltip(props) {
    const [value, setValue] = React.useState(null);
  
    return (
        <ElementContainer>
            <TextFieldContainer>
            <LocalizationProvider dateAdapter={AdapterDateFns}>
                <DatePicker
                label={props.label}
                value={value}
                inputFormat="dd/MM/yyyy"
                onChange={(newValue) => {
                    setValue(newValue);
            
                    // Essentially a massive hack to make datepicker play nice with our onchange function
                    var fakeEvent = new Object();
                    fakeEvent.target = {
                        name: props.name,
                        value: newValue
                    };
                    props.onChange(fakeEvent)
                }}
                renderInput={(params) => <TextField size="small" {...params} />}
                />
            </LocalizationProvider>
            </TextFieldContainer>
            <QMarkContainer>
            <TooltipQMark title={props.tooltip}/>
            </QMarkContainer>
        </ElementContainer>
    );
  }