import React from 'react';
import MetadataEntryBar from './MetadataEntryBar';
import ClaimPageView from '../components/ClaimPageView';
import styled from 'styled-components';
import { TourProvider } from "@reactour/tour";
import TourWrapper from '../components/TourWrapper';
import axios from "axios";
import { Redirect } from "react-router-dom";
import config from "../config.json"
import { notEmptyValidator, atLeastOneValidator, emptyOrValidUrlValidator } from '../utils/validation.js'
import ClaimNormalizationConfirmation from './ClaimNormalizationConfirmation'

const NEntryBar = styled(MetadataEntryBar)`
    width: -webkit-calc(40% - 10px)!important;
    width:    -moz-calc(40% - 10px)!important;
    width:         calc(40% - 10px)!important;
    float:left;
    overflow:auto;
    border-style:inset;
    border-width:2px;
    height: -webkit-calc(100% - 6px)!important;
    height:    -moz-calc(100% - 6px)!important;
    height:         calc(100% - 6px)!important;
`

const NPageView = styled(ClaimPageView)`
    width:60%;
    float:left;
    border-style:inset;
    border-width:2px;
    height: -webkit-calc(100% - 6px)!important;
    height:    -moz-calc(100% - 6px)!important;
    height:         calc(100% - 6px)!important;
`

const PageDiv = styled.div`
    width: 100%;
    height: 100vh;
`

function validate(content) {
    var valid = true

    Object.values(content["entries"]).forEach(entry => {
        if (!("fact_checker_strategy" in entry) || atLeastOneValidator(entry["fact_checker_strategy"]).error) {
            valid = false;
        } else if (!("claim_types" in entry) || atLeastOneValidator(entry["claim_types"]).error) {
            valid = false;
        } else if (!("phase_1_label" in entry) || notEmptyValidator(entry["claim_types"]).error) {
            valid = false;
        } else if (emptyOrValidUrlValidator(entry["hyperlink"]).error) {
            valid = false;
        } else if (emptyOrValidUrlValidator(entry["media_source"]).error) {
            valid = false;
        }
    });

    return valid;
}

class ClaimNormalization extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            claim: {
                web_archive: ""
            },
            entries: {
                "claim_entry_field_1": {}
            },
            claim_header: {},
            claim_footer: {},
            userIsFirstVisiting: false,
            added_entries: 1,
            valid: true,
            final_idx: 0,
            confirmation: false,
            startTime: new Date()
        }

        this.doSubmit = this.doSubmit.bind(this);
        this.proceedToConfirmation = this.proceedToConfirmation.bind(this);
        this.cancelConfirmation = this.cancelConfirmation.bind(this);
        this.handleFieldChange = this.handleFieldChange.bind(this);
        this.deleteEntry = this.deleteEntry.bind(this);
        this.addEntry = this.addEntry.bind(this);
    }

    deleteEntry = (entryId) => {
        let entries = this.state.entries
        delete entries[entryId]

        this.setState({
            entries: entries
        });
    }

    addEntry = () => {
        const field_id = "claim_entry_field_" + this.state.added_entries
        console.log("adding entry \"" + field_id + "\"")

        this.setState({
            entries: {
                ...this.state.entries,
                [field_id]: {}
            },
            added_entries: this.state.added_entries + 1
        });
    }

    setEntries = (newEntries) => {
        this.setState({
            entries: newEntries
        })
    }

    handleFieldChange(fieldId, element, value) {
        // console.log(fieldId)
        if (fieldId === "claim_header") {
            this.setState(prevState => ({
                [fieldId]: {
                    ...prevState[fieldId],
                    [element]: value
                }
            }))
        } else if (fieldId === "claim_footer") {
            this.setState(prevState => ({
                [fieldId]: {
                    ...prevState[fieldId],
                    [element]: value
                }
            }))
        } else {
            this.setState(prevState => ({
                entries: {
                    ...prevState.entries,
                    [fieldId]: {
                        ...prevState.entries[fieldId],
                        [element]: value
                    }
                }
            }))
        }
    }

    changeLabel = event => {
        const { name, value } = event.target;

        this.setState({
            qa_pair_footer: {
                ...this.state.qa_pair_footer,
                label: value
            }
        })
    }

    async doSubmit() {
        var dataset = "annotation"
        if (this.props.dataset) {
            dataset = this.props.dataset
        }

        var current_idx = Number(localStorage.finished_norm_annotations) + 1 - Number(localStorage.pc);

        if (dataset === "training") {
            current_idx = Number(localStorage.train_finished_norm_annotations) + 1 - Number(localStorage.pc);
        }

        let is_at_last_claim = current_idx === this.state.final_idx;
        let should_use_finish_path = this.props.finish_path && is_at_last_claim

        console.log("Submit stuff")
        console.log(should_use_finish_path)
        console.log(this.props.finish_path)
        console.log(current_idx)
        console.log(this.state.final_idx)

        // e.preventDefault();
        console.log("Valid: " + validate(this.state));
        if (validate(this.state)) {
            let pc = Number(localStorage.pc);
            var request = {}
            if (pc !== 0) {
                localStorage.pc = Number(localStorage.pc) - 1;
                var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/claim_norm.php",
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        req_type: 'resubmit-data',
                        dataset: dataset,
                        entries: this.state.entries,
                        claim_id: localStorage.claim_id,
                        startTime: this.state.startTime.toUTCString(),
                        submitTime: new Date().toUTCString()
                    }
                };

                await axios(request).then((response) => {
                    console.log(response.data);
                    localStorage.claim_id = 0;

                    if (should_use_finish_path) {
                        window.location.assign(this.props.finish_path);
                    } else {
                        window.location.reload(false);
                    }
                }).catch((error) => { window.alert(error) })
            } else {
                var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/claim_norm.php",
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        dataset: dataset,
                        req_type: 'submit-data',
                        entries: this.state.entries,
                        startTime: this.state.startTime.toUTCString(),
                        submitTime: new Date().toUTCString()
                    }
                };

                await axios(request).then((response) => {

                    if (dataset === "training") {
                        localStorage.train_finished_norm_annotations = Number(localStorage.train_finished_norm_annotations) + 1;
                    } else {
                        localStorage.finished_norm_annotations = Number(localStorage.finished_norm_annotations) + 1;
                    }
                    console.log(response.data);

                    if (should_use_finish_path) {
                        window.location.assign(this.props.finish_path);
                    } else {
                        window.location.reload(false);
                    }
                }).catch((error) => { window.alert(error) })
            }
        } else {
            this.setState({
                valid: false
            });
        }
    }

    componentDidMount() {
        localStorage.setItem('phase', "phase_1");
        var dataset = "annotation"
        if (this.props.dataset) {
            dataset = this.props.dataset
        }

        console.log(dataset)

        if (localStorage.getItem('login')) {
            if (this.props.finish_at) {
                this.setState({
                    final_idx: this.props.finish_at
                })
            } else {
                var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/user_statistics.php",
                    data: {
                        logged_in_user_id: localStorage.getItem('user_id'),
                        dataset: dataset,
                        req_type: 'get-statistics',
                        get_by_user_id: localStorage.getItem('user_id')
                    }
                };

                axios(request).then((response) => {
                    console.log(response)
                    this.setState({
                        final_idx: response.data.phase_1.annotations_assigned
                    })

                }).catch((error) => { window.alert(error) });
            }

            let pc = Number(localStorage.pc);
            console.log(pc);
            if (pc !== 0) {
                var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/claim_norm.php",
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        dataset: dataset,
                        req_type: 'reload-data',
                        offset: pc - 1
                    }
                };

                axios(request).then((response) => {
                    if (response.data) {
                        console.log(response.data);
                        const new_claim = { web_archive: response.data.web_archive };

                        localStorage.claim_id = response.data.claim_id;
                        console.log("I'm seeing a claim with id " + response.data.claim_id)
                        this.setState({ claim: new_claim });

                        var new_entries = response.data.entries;

                        for (var key in new_entries) {
                            var entry = new_entries[key];
                            if (entry.date) {
                                entry.date = new Date(entry.date + "T00:00:00.0Z");
                            }
                        }
                        this.setState({ entries: new_entries });

                        // TODO: This is a terrible hack to get around not currently storing added_entries in the DB. Please fix later.
                        var new_entry_count = 0
                        Object.keys(new_entries).forEach(key => {
                            var n = parseInt(key.split("_").at(-1))

                            new_entry_count = Math.max(new_entry_count, n)
                        })
                        new_entry_count += 1
                        this.setState({ added_entries: new_entry_count });

                        console.log(this.state);
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {
                    window.alert(error)
                })
            } else {
                console.log("I'm getting new data for dataset " + dataset)
                var request = {
                    method: "post",
                    baseURL: config.api_url,
                    url: "/claim_norm.php",
                    data: {
                        user_id: localStorage.getItem('user_id'),
                        dataset: dataset,
                        req_type: 'next-data'
                    }
                };

                axios(request).then((response) => {
                    console.log(response.data);
                    if (response.data) {
                        let finished_annotations = Number(localStorage.finished_norm_annotations)
                        if (dataset === "training") {
                            finished_annotations = Number(localStorage.train_finished_norm_annotations)
                        }

                        if (finished_annotations === 0) {
                            this.setState({ userIsFirstVisiting: true });
                        }
                        const new_claim = { web_archive: response.data.web_archive };
                        localStorage.claim_id = response.data.claim_id;
                        console.log("I'm seeing a claim with id " + response.data.claim_id)
                        this.setState({ claim: new_claim });
                        const new_entries = { "claim_entry_field_0": {} };
                        this.setState({ entries: new_entries });
                    } else {
                        window.alert("No more claims!");
                    }
                }).catch((error) => {
                    window.alert(error)
                })
            }
        }
    }

    proceedToConfirmation() {
        console.log("Valid: " + validate(this.state));

        if (validate(this.state)) {
            if (this.needsConfirmation()) {
                this.setState({
                    confirmation: true
                });
            } else {
                this.doSubmit()
            }
        } else {
            this.setState({
                valid: false
            });
        }
    }

    cancelConfirmation() {
        this.setState({
            confirmation: false
        });
    }

    needsConfirmation() {
        let should_confirm = false;
        Object.keys(this.state.entries).forEach(claim_id =>
            should_confirm = should_confirm || this.state.entries[claim_id].transcription
            || (this.state.entries[claim_id].claim_types
                && (this.state.entries[claim_id].claim_types.includes("Speculative Claim") || this.state.entries[claim_id].claim_types.includes("Opinion Claim")))
            || (this.state.entries[claim_id].fact_checker_strategy
                && this.state.entries[claim_id].fact_checker_strategy.includes("Media Source Discovery"))
        )

        return should_confirm
    }

    render() {
        var dataset = "annotation"
        if (this.props.dataset) {
            dataset = this.props.dataset
        }
        let finished_annotations = Number(localStorage.finished_norm_annotations)
        if (dataset === "training") {
            finished_annotations = Number(localStorage.train_finished_norm_annotations)
        }

        var current_idx = finished_annotations + 1 - Number(localStorage.pc);

        console.log("Current idx:")
        console.log(current_idx)

        if (!localStorage.getItem('login')) {
            return <Redirect to='/' />;
        }

        const steps = [
            {
                selector: '[data-tour="claim_page_view"]',
                content: "Begin by carefully reading the fact-checking article."
            },
            {
                selector: '[data-tour="report"]',
                content: "If the fact-checking article shows a 404 page or another error, or if the article is behind a paywall, you can report it to us (although please give it a minute to load - some sites are not very fast)."
            },
            {
                selector: '[data-tour="claim_textfield"]',
                content: "Fill in the text of the main claim the article is dealing with. Please edit the claim according to the instructions, but otherwise change it as little as possible. If there is a discrepancy between the original claim (e.g. a claim posted to Twitter) and the text of the claim in the fact-checking article, please stick as closely as possible to the original wording."
            },
            {
                selector: '[data-tour="verdict"]',
                content: "Select the closest verdict to the one chosen by the fact-checking article. Please mirror their verdict as well as you can, even if you disagree with it."
            },
            {
                selector: '[data-tour="metadata_fields"]',
                content: "Using the fact-checking article, fill in the rest of the data collection fields..."
            },
            {
                selector: '[data-tour="metadata_fields_2"]',
                content: "... and select the most appropriate claim types and fact checking strategies."
            },
            {
                selector: '[data-tour="add"]',
                content: "If the fact-checking article covers more than one claim, you can add additional claims. Some claims consist of multiple, easily separable, independent parts (e.g. \"The productivity rate in Scotland rose in 2017, and similarly productivity rose in Wales that year.\"). Please split these claims into their parts."
            },
            {
                selector: '[data-tour="submit"]',
                content: "When you have added all claims from this fact-checking article, submit your claims to proceed to the next article."
            },
        ];

        return (
            <PageDiv>
                {!this.state.confirmation ?
                    <TourProvider steps={steps}>
                        <NPageView claim={this.state.claim} phase={1} />
                        <NEntryBar
                            handleFieldChange={this.handleFieldChange}
                            current_idx={current_idx}
                            final_idx={this.state.final_idx}
                            claim={this.state.claim}
                            entries={this.state.entries}
                            addEntry={this.addEntry}
                            deleteEntry={this.deleteEntry}
                            doSubmit={this.proceedToConfirmation}
                            header={this.state.claim_header}
                            footer={this.state.claim_footer}
                            valid={this.state.valid}
                            dataset={dataset} 
                            finish_path={this.props.finish_path}
                        />
                        {this.state.userIsFirstVisiting ? <TourWrapper /> : ""}
                    </TourProvider>
                    :
                    <ClaimNormalizationConfirmation confirmFunction={this.doSubmit} cancelFunction={this.cancelConfirmation} current_idx={current_idx} final_idx={this.state.final_idx} entries={this.state.entries} />
                }
            </PageDiv>
        );
    }
}

export default ClaimNormalization;
