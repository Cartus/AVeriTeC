import NoteScreen from "../note_screen/NoteScreen";
import React, { useState, useEffect } from 'react';
import Card from '@material-ui/core/Card';
import styled from 'styled-components';
import * as react from "react";
import { DataGrid } from '@mui/x-data-grid';
import Button from '@material-ui/core/Button';
import DeleteIcon from '@material-ui/icons/Delete';
import axios from "axios";
import config from "../config.json"
import { BarChart, Bar, Cell, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';



const EntryCard = styled(Card)`
    margin:10px;
    padding: 0px 20px 5px 20px;
`

const Header = styled.h4`
`

const ChartBox = styled("div")`
    float: left;
`


export default function UserTaskPerformanceOverview(props) {


    const metricChartKeys = {
        average_training_label_agreement: "Training label agreement",
        average_training_claim_overlap_rouge: "Training claim ROUGE-L",
        average_training_strategy_f1: "Training strategy f1",
        average_training_claim_type_f1: "Training type f1",
        average_training_question_overlap_rouge: "Training question ROUGE-L",
        average_training_answer_overlap_rouge: "Training answer ROUGE-L",
        average_agreement_with_p3_annotators: "Label agreement w/ P3",
        average_agreement_with_p2_annotators: "Label agreement w/ P2",
    }

    

    var chartData = []
    if (props.userStats) {
        Object.keys(props.userStats).forEach(key => {
            if (key in props.userStats && key in props.averageStats && key in metricChartKeys) {
                chartData = [
                    ...chartData,
                    {
                        name: metricChartKeys[key],
                        user: props.userStats[key],
                        average: props.averageStats[key]
                    }
                ]
            }
        })
    }

    const countChartKeys = {
        annotations_done: "Annotations completed",
        annotations_assigned: "Annotations assigned",
        claims_skipped: "Annotations skipped",
        annotations_timed_out: "Annotations timed out",
        speed_traps_hit: "Speed traps hit",
    }

    var countChartData = []
    if (props.userStats) {
        Object.keys(props.userStats).forEach(key => {
            if (key in props.userStats && key in props.averageStats && key in countChartKeys) {
                countChartData = [
                    ...countChartData,
                    {
                        name: countChartKeys[key],
                        user: props.userStats[key],
                        average: props.averageStats[key]
                    }
                ]
            }
        })
    }

    var timeData = []
    if (props.userStats) {
        timeData = [
            {
                name: "Average load time",
                user: props.userStats.average_load_time,
                average: props.averageStats.average_load_time,
            }, {
                name: "Average task time",
                user: props.userStats.average_task_time,
                average: props.averageStats.average_task_time
            }
        ]
    }


    return <EntryCard>
        <Header>{props.name}</Header>
        {chartData.length > 0 ?
            <ChartBox>
                <BarChart
                    width={225 * chartData.length + 100}
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
                    <Bar name="User" dataKey="user" fill="#8884d8" />
                    <Bar name="Average" dataKey="average" fill="#82ca9d" />
                </BarChart>
            </ChartBox>
            :
            ""}
        {countChartData.length > 0 ?
            <ChartBox>
                <BarChart
                    width={225 * countChartData.length + 100}
                    height={300}
                    data={countChartData}
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
                    <Bar name="User" dataKey="user" fill="#8884d8" />
                    <Bar name="Average" dataKey="average" fill="#82ca9d" />
                </BarChart>
            </ChartBox>
            :
            ""}
        {timeData.length > 0 ?
            <ChartBox>
                <BarChart
                    width={225 * timeData.length + 100}
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
                    <YAxis unit=" s" />
                    <Tooltip />
                    <Legend />
                    <Bar name="User" dataKey="user" fill="#8884d8" unit=" s" />
                    <Bar name="Average" dataKey="average" fill="#82ca9d" unit=" s" />
                </BarChart>
            </ChartBox>
            :
            ""}
    </EntryCard >;
}