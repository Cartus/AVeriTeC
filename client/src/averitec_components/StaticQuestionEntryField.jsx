import React from 'react';
import styled from 'styled-components';
import Grid from '@material-ui/core/Grid';
import Divider from '@material-ui/core/Divider';
import TextField from '@material-ui/core/TextField';

const ColumnDiv = styled.div`
    width:100%;
    margin: 20px;
`

const QAGrid = styled(Grid)`
    float: left;
    width: -webkit-calc(100% - 16px)!important;
    width:    -moz-calc(100% - 16px)!important;
    width:         calc(100% - 16px)!important;
`

const QAGridElementLeft = styled(Grid)`
    width: -webkit-calc(51%)!important;
    width:    -moz-calc(51%)!important;
    width:         calc(51%)!important;
`

const PaddingDiv = styled.div`
    width:63px;
`

const QAGridElementRight = styled(Grid)`
    width: -webkit-calc(49% - 63px)!important;
    width:    -moz-calc(49% - 63px)!important;
    width:         calc(49% - 63px)!important;
`

class StaticQuestionEntryField extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
              <QAGrid container direction="row" justifyContent="space-evenly" alignItems="center" spacing={3}>
                <QAGridElementLeft item xs>
                    <ColumnDiv>
                        <TextField name='question' label="Question" InputProps={{readOnly: true}} variant="filled"  value={this.props.question.text} multiline rows={2}/>
                    </ColumnDiv>
                </QAGridElementLeft>
                <PaddingDiv/>
                <Divider orientation="vertical" flexItem />
                <QAGridElementRight item xs>
                    <ColumnDiv>
                        <TextField name='answer' label="Answer" InputProps={{readOnly: true}} variant="filled" value={this.props.question.answer} multiline rows={2}/>
                    </ColumnDiv>
                </QAGridElementRight>
            </QAGrid>
            </div>
        );
      }
}

export default StaticQuestionEntryField