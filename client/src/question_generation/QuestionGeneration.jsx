import React from 'react';
import QuestionGenerationBar from './QuestionGenerationBar';
import ClaimPageView from '../components/ClaimPageView';

class QuestionGeneration extends React.Component {
    constructor(props) {
        super(props);
        
        this.state = {
            claim : {
                web_archive: "https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/"
            }
        }
      }

    render() {
        return (
            <div>
                <QuestionGenerationBar/>
                <ClaimPageView claim={this.state.claim}/>
            </div>
        );
      }
}

export default QuestionGeneration;