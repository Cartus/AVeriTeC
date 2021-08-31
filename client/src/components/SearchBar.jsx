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
      }

    render() {
        return (
            <div style={{position: 'relative', display: 'inline-block'}}>
                <TextField
                hintText="Search by Name"
                InputProps={{endAdornment: <SearchButton><SearchIcon/></SearchButton>}}
                {...this.props}
                />
            </div>
        );
      }
}

export default SearchBar;