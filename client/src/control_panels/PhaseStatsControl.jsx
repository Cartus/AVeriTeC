import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import * as react from "react";
import { DataGrid } from '@mui/x-data-grid';
import Button from '@material-ui/core/Button';
import DeleteIcon from '@material-ui/icons/Delete';
import axios from "axios";
import config from "../config.json"
import {
    AreaChart,
    Area,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip, LineChart, Line, Legend, ResponsiveContainer,
    BarChart, Bar, Cell
} from "recharts";

const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px 5px 20px;
`

const Header = styled.h4`
`

const AddButton = styled(Button)`
float:left;
width:200px;
margin:10px 0px!important;
`

const JsonButton = styled(Button)`
float:left;
width:200px;
margin:10px!important;
`

const DeleteButton = styled(Button)`
float:right;
width:200px;
margin:10px 0px!important;
`

const ChartBox = styled("div")`
    float: left;
`


class PhaseStatsControl extends react.Component {
    constructor(props) {
        super(props);

        this.state = {
            avg_times: {
                load: 0.87,
                finish: 1.9
            },
            phase_eval_stats: {
            },
            annotation_data: [
                {
                    name: 'Week 1',
                    "Annotations pending": 7500,
                    "Annotations assigned": 0,
                    "Annotations completed": 0,
                    "Claims skipped": 0,
                },
                {
                    name: 'Week 2',
                    "Annotations pending": 4000,
                    "Annotations assigned": 500,
                    "Annotations completed": 3000,
                    "Claims skipped": 20,
                },
                {
                    name: 'Week 3',
                    "Annotations pending": 3000,
                    "Annotations assigned": 1500,
                    "Annotations completed": 3000,
                    "Claims skipped": 100,
                },
                {
                    name: 'Week 4',
                    "Annotations pending": 1000,
                    "Annotations assigned": 2000,
                    "Annotations completed": 5500,
                    "Claims skipped": 120,
                },
                {
                    name: 'Week 5',
                    "Annotations pending": 0,
                    "Annotations assigned": 2000,
                    "Annotations completed": 5500,
                    "Claims skipped": 150,
                },
                {
                    name: 'Week 6',
                    "Annotations pending": 0,
                    "Annotations assigned": 1000,
                    "Annotations completed": 6500,
                    "Claims skipped": 170,
                },
                {
                    name: 'Week 7',
                    "Annotations pending": 0,
                    "Annotations assigned": 500,
                    "Annotations completed": 7000,
                    "Claims skipped": 200,
                },
                {
                    name: 'Week 8',
                    "Annotations pending": 0,
                    "Annotations assigned": 0,
                    "Annotations completed": 7500,
                    "Claims skipped": 240,
                }
            ]
        }
    }

    componentDidMount() {
        if (this.props.phase == 1) {
            this.setState({
                phase_eval_stats: {
                    average_training_label_agreement: 0.7,
                    average_training_claim_overlap_rouge: 0.4,
                    average_training_strategy_f1: 0.5,
                    average_training_claim_type_f1: 0.9
                }
            })
        } else if (this.props.phase == 2) {
            this.setState({
                phase_eval_stats: {
                    average_training_label_agreement: 0.2,
                    average_training_question_overlap_rouge: 0.1,
                    average_training_answer_overlap_rouge: 1.0,
                    average_agreement_with_p3_annotators: 0.5
                }
            })
        } else if (this.props.phase == 3) {
            this.setState({
                phase_eval_stats: {
                    average_training_label_agreement: 0.7,
                    average_agreement_with_p2_annotators: 0.5
                }
            })
        }

    }

    render() {
        let className = ''

        if (this.props.className !== undefined) {
            className = this.props.className
        }

        const timekeeping_avg = {
            Task: 4.2,
            Loading: 1.2
        }

        var chartData = []

        const chart_keys = {
            average_training_label_agreement: "Training label agreement",
            average_training_claim_overlap_rouge: "Training claim ROUGE-L",
            average_training_strategy_f1: "Training strategy f1",
            average_training_claim_type_f1: "Training type f1",
            average_training_question_overlap_rouge: "Training question ROUGE-L",
            average_training_answer_overlap_rouge: "Training answer ROUGE-L",
            average_agreement_with_p3_annotators: "Label agreement w/ P3",
            average_agreement_with_p2_annotators: "Label agreement w/ P2",
        }

        if (this.state.phase_eval_stats) {
            Object.keys(this.state.phase_eval_stats).forEach(key => {
                chartData = [
                    ...chartData,
                    {
                        name: chart_keys[key],
                        user: "average",
                        average: this.state.phase_eval_stats[key]
                    }
                ]
            })
        }

        var timeData = []
        if (this.state.avg_times) {
            timeData = [
                {
                    name: "Average load time",
                    average: this.state.avg_times.load,
                }, {
                    name: "Average task time",
                    average: this.state.avg_times.finish
                }
            ]
        }

        return (
            <div className={className}>
                <EntryCard>
                    <Header>{this.props.name}</Header>
                    <ChartBox>
                        <LineChart
                            width={500}
                            height={300}
                            data={this.state.annotation_data}
                            margin={{
                                top: 5,
                                right: 30,
                                left: 20,
                                bottom: 5,
                            }}
                        >
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis dataKey="name" />
                            <YAxis />
                            <Tooltip />
                            <Legend />
                            <Line type="monotone" dataKey="Annotations completed" stroke="#8884d8" activeDot={{ r: 8 }} />
                            <Line type="monotone" dataKey="Annotations assigned" stroke="#82ca9d" activeDot={{ r: 8 }} />
                            <Line type="monotone" dataKey="Annotations pending" stroke="#ed6145" activeDot={{ r: 8 }} />
                            <Line type="monotone" dataKey="Claims skipped" stroke="#e0cc19" activeDot={{ r: 8 }} />
                        </LineChart>
                    </ChartBox>

                    {timeData.length > 0 ?
                        <ChartBox>
                            <BarChart
                                width={200 * timeData.length + 100}
                                height={300}
                                data={timeData}
                                margin={{
                                    top: 5,
                                    right: 30,
                                    left: 20,
                                    bottom: 5,
                                }}
                            >
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis dataKey="name" />
                                <YAxis />
                                <Tooltip />
                                <Legend />
                                <Bar barSize={60} name="Average" dataKey="average" fill="#82ca9d" unit=" s" />
                            </BarChart>
                        </ChartBox>
                        :
                        ""}

                    {chartData.length > 0 ?
                        <ChartBox>
                            <BarChart
                                width={200 * chartData.length + 100}
                                height={300}
                                data={chartData}
                                margin={{
                                    top: 5,
                                    right: 30,
                                    left: 20,
                                    bottom: 5,
                                }}
                            >
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis dataKey="name" />
                                <YAxis />
                                <Tooltip />
                                <Legend />
                                <Bar barSize={60} name="Average" dataKey="average" fill="#82ca9d" />
                            </BarChart>
                        </ChartBox>
                        :
                        ""}
                </EntryCard >
            </div >
        );
    }
}

export default PhaseStatsControl;
