import React from 'react';
import styled from 'styled-components';
import SearchBar from './SearchBar';
import Card from '@material-ui/core/Card';
import Pagination from '@material-ui/lab/Pagination';
import axios from 'axios';

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
            page: 1,
            didSearch: false,
            searchItems: [
            ]
        };

        
        this.doSearch = this.doSearch.bind(this);
        this.handlePageChange = this.handlePageChange.bind(this);
      }

    doSearch = () => {
        var query = this.state.searchQuery
        var claim_date = this.props.claim_date
        var page = this.state.page

        this.setState({didSearch: true});

        var request = {
            method: "get",
            url: "http://api.averitec.eu/web_search.php",
            params:{
                query: query,
                claim_date:claim_date,
                page: page
            }
        };

        axios(request).then((response) => {
            this.setState({
                searchItems: response.data.items
            });
        }).catch((error) => {window.alert(error)})
    }

    handlePageChange = (event, newPage) => {
        this.setState({
            page: newPage
          }, () => {this.doSearch()});
    }

    render() {
        let className = ''

        if(this.props.className !== undefined){
            className = this.props.className
        }

        var searchResults = ""

        if (this.state.didSearch){
            searchResults = <div><SearchItemContainer searchItems={this.state.searchItems}></SearchItemContainer>
            <CenterPagination 
            count={10} 
            shape="rounded"
            page={this.state.page}
            onChange={this.handlePageChange} />
            </div>
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
                {searchResults}
            </div>
        );
      }
}

export default SearchField;