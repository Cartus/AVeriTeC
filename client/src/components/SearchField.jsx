import React from 'react';
import styled from 'styled-components';
import SearchBar from './SearchBar';
import Card from '@material-ui/core/Card';
import Pagination from '@material-ui/lab/Pagination';

const SearchItemCard = styled(Card)`
  margin:10px;
  padding:10px;
`

const CenterSearchBar = styled(SearchBar)`
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 45px 15%;
    width:70%;
`
const CenterPagination = styled(Pagination)`
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 30px 0px;
`

const SearchItemHeader = styled.h4`
    margin:9px 0px;
    text-decoration: underline;
`

const SearchLink = styled.a`
    text-decoration: none;
`

const BreadcrumbSpan = styled.span`
    text-decoration: none;
    color: #484848;
    font-size:small;
`

function ItemCard(item){
    if (item.url.match(/^https?:\/\//)){
        var source_url = item.url.split("/").slice(0,3).join('/')
        var displayed_url = source_url
        var breadcrumbs = item.url.split("/").slice(3).join('/').replace("/", " > ")
    } else{
        var source_url = "http://" + item.url.split("/")[0]
        var displayed_url = item.url.split("/")[0]
        var breadcrumbs = item.url.split("/").slice(1).join('/').replace("/", " > ")
    }
    
    if (breadcrumbs.length > 0){
        breadcrumbs = " > " + breadcrumbs
    }

    const allowed_length = 256
    let abstract = item.abstract.trim()
    if (abstract.length > allowed_length){
        abstract = abstract.substring(0, allowed_length) + "..."
    }

    return (<SearchItemCard key={item.url} variant="outlined">
        <SearchLink target="_blank" rel="noopener noreferrer" href={source_url}>
            <BreadcrumbSpan>{displayed_url}</BreadcrumbSpan>
            <BreadcrumbSpan>{breadcrumbs}</BreadcrumbSpan>
            <SearchItemHeader>{item.header}</SearchItemHeader>
        </SearchLink>
        <div>
            {abstract}
        </div>
        </SearchItemCard>);
}

function SearchItemContainer(props) {
    const searchItemCards = props.searchItems.map(item => ItemCard(item));

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
                    url: "https://abd.def.com/ahfhue",
                    header: "ASDF1",
                    abstract: "asdf asdf asdf asdf"
                },
                {
                    url: "http://afef.vrgsr.com/rgrs/afee",
                    header: "ASDF2",
                    abstract: "asdf ahttttaaaeesdf asdf asdf asdf ahttttaaaeesdf asdf asdf asdf ahttttaaaeesdf asdf asdf asdf ahttttaaaeesdf asdf asdf asdf ahttttaaaeesdf asdf asdf asdf ahttttaaaeesdf asdf asdf asdf ahttttaaaeesdf asdf asdf ahttttaaaeesdf asdf asdf ahttttaaaeesdf asdf asdf ahttttaaaeesdf asdf asdf ahttttaaaeesdf asdf asdf"
                },
                {
                    url: "afef.vrr.ujucom/rgrs/iii",
                    header: "ASDF3",
                    abstract: "aaaesdf asdf afeeeaefafsdf asdf"
                },
                {
                    url: "arxiv.org/",
                    header: "ASDF4",
                    abstract: "aaaesdf asdf afeeeaefafsdf asdf"
                },
            ]
        };

        
        this.doSearch = this.doSearch.bind(this);
      }

    doSearch = () => {
    }

    render() {
        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        return (
            <div style={this.props.style} className={className}>
                <CenterSearchBar
                value={this.state.searchQuery}
                onChange={(newSearchQuery) => this.setState({ searchQuery: newSearchQuery.target.value })}
                onRequestSearch={() => this.doSearch(this.state.searchQuery)}
                label="Search"
                fullWidth
                />
                <SearchItemContainer searchItems={this.state.searchItems}></SearchItemContainer>
                <CenterPagination count={10} shape="rounded" />
            </div>
        );
      }
}

export default SearchField;