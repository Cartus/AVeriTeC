import React from 'react';
import styled from 'styled-components';
import TextField from '@material-ui/core/TextField';
import SearchIcon from '@material-ui/icons/Search';
import IconButton from '@material-ui/core/IconButton';

const SearchButton = styled(IconButton)`
  position: 'absolute';
  right:100;
  top: 15;
  width:20;
  height:20;
`


class SearchBar extends React.Component {
    constructor(props) {
        super(props);
        this.handleKeyPress = this.handleKeyPress.bind(this);
      }

    handleKeyPress(e){
        if (e.key === 'Enter'){
            this.props.onRequestSearch();
        }
    }

    render() {
        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        return (
            <div className={className}>
                <TextField
                hintText="Search by Name"
                onKeyUp={this.handleKeyPress}
                InputProps={{endAdornment: <SearchButton onClick={this.props.onRequestSearch} ><SearchIcon/></SearchButton>}}
                {...this.props}
                />
            </div>
        );
      }
}

export default SearchBar;