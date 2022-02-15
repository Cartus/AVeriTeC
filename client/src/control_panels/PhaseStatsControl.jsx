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
    Tooltip, LineChart, Line, Legend, ResponsiveContainer
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
    width: 510px;
    float: left;
`


class PhaseStatsControl extends react.Component {
    constructor(props) {
        super(props);

        this.state = {
            avg_times: {
                load: 0.87,
                finish: 1.9
            }
        }
    }

    componentDidMount() {

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

        const annotation_data = [
            {
                name: 'Week 1',
                "Annotations completed": 4000,
            },
            {
                name: 'Week 2',
                "Annotations completed": 5000,
            },
            {
                name: 'Week 3',
                "Annotations completed": 6000,
            },
            {
                name: 'Week 4',
                "Annotations completed": 8000,
            },
            {
                name: 'Week 5',
                "Annotations completed": 14000,
            },
            {
                name: 'Week 6',
                "Annotations completed": 15000,
            },
            {
                name: 'Week 7',
                "Annotations completed": 15000,
            },
            {
                name: 'Week 8',
                "Annotations completed": 16000,
            },
        ];

        return (
            <div className={className}>
                <EntryCard>
                    <Header>{this.props.name}</Header>
                    <ChartBox>
                        <span>
                            Average time spent loading: {timekeeping_avg.Loading}
                        </span>
                        <br/>
                        <br/>
                        <span>
                            Average time spent on task: {timekeeping_avg.Task}
                        </span>
                        <br/>
                        <br/>
                        <span>
                            Performance metrics:
                        </span>
                    </ChartBox>
                    <ChartBox>
                        <LineChart
                            width={500}
                            height={300}
                            data={annotation_data}
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
                        </LineChart>
                    </ChartBox>
                </EntryCard >
            </div >
        );
    }
}

export default PhaseStatsControl;
