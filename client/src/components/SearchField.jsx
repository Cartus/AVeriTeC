import React from 'react';
import styled from 'styled-components';
import SearchBar from './SearchBar';
import Card from '@material-ui/core/Card';
import Pagination from '@material-ui/lab/Pagination';

const SearchItemCard = styled(Card)`
  margin:10px;
`

function SearchItemContainer(props) {
    const searchItemCards = props.searchItems.map(item => (
            <SearchItemCard key={item.url} variant="outlined">                
                <a target="_blank" rel="noopener noreferrer" href={item.url}>
                    <cite>{((item.url.startsWith("http"))? item.url.split("/").slice(0,3).join('/'): item.url.split("/")[0])}</cite>
                    <span>{" > " + item.url.split("/").slice(3).join('/').replace("/", " > ")}</span>
                    <h4>{item.header}</h4>
                </a>
                <div>
                    {item.abstract}
                </div>
            </SearchItemCard>
          ));

        return (
            <div>
              {searchItemCards}
            </div>
        );
}

class SearchField extends React.Component {
    constructor(props) {
        super(props);

        /* Warning: I'm lazy, so there is a hack here; the site will crash if urls are not unique. We should ensure they are in any case though        
        */

        this.state = {
            searchQuery: "",
            searchItems: [
                {
                    url: "http://abd.def.com/ahfhue",
                    header: "ASDF1",
                    abstract: "asdf asdf asdf asdf"
                },
                {
                    url: "http://afef.vrgsr.com/rgrs/afee",
                    header: "ASDF2",
                    abstract: "asdf ahttttaaaeesdf asdf asdf"
                },
                {
                    url: "afef.vrr.ujucom/rgrs/iii",
                    header: "ASDF3",
                    abstract: "aaaesdf asdf afeeeaefafsdf asdf"
                },
            ]
        };

        
        this.doSearch = this.doSearch.bind(this);
      }

    doSearch = () => {
    }

    render() {
        return (
            <div>
                <SearchBar
                value={this.state.searchQuery}
                onChange={(newSearchQuery) => this.setState({ searchQuery: newSearchQuery.target.value })}
                onRequestSearch={() => this.doSearch(this.state.searchQuery)}
                label="Search"
                />
                <SearchItemContainer searchItems={this.state.searchItems}></SearchItemContainer>
                <Pagination count={10} shape="rounded" />
            </div>
        );
      }
}

export default SearchField;