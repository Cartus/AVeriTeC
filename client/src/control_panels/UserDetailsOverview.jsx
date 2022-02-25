import NoteScreen from "../note_screen/NoteScreen";
import React, { useState, useEffect } from 'react';
import UserTaskPerformanceOverview from "./UserTaskPerformanceOverview";
import config from "../config.json"
import axios from "axios";

export default function UserDetailsOverview(props) {
    const [user, setUser] = useState({});
    const [taskStats, setTaskStats] = useState({});

    useEffect(() => {
        var userId = new URLSearchParams(window.location.search).get("id")
        console.log("Getting data for user with ID \'" + userId + "\'.")

        // Todo: get user by id from the database, along with stats.
        // We can compute avgs and rouge here or in backend, which is easiest.
        // Todo: get this only if admin. Else, show access denied error and redirect to /.

        var request = {
            method: "post",
            baseURL: config.api_url,
            url: "/user_statistics.php",
            data: {
                logged_in_user_id: localStorage.getItem('user_id'),
                req_type: 'get-statistics',
                get_by_user_id: userId
            }
        };

        axios(request).then((response) => {
            if (response.data.is_admin === false) {
                window.alert("Error: Access denied.")
            } else {
                // Replace with:
                setUser(response.data);
                /*setUser({
                    username: "Michael",
                    phase_1: {
                        annotations_done: 5,
                        annotations_assigned: 10,
                        claims_skipped: 1,
                        annotations_timed_out: 0,
                        speed_traps_hit: 0,
                        average_training_label_agreement: 0.8,
                        average_training_claim_overlap_rouge: 0.3,
                        average_training_strategy_f1: 0.8,
                        average_training_claim_type_f1: 0.8,
                        average_load_time: 2,
                        average_task_time: 5
                    },
                    phase_2: {
                        annotations_done: 5,
                        annotations_assigned: 10,
                        claims_skipped: 1,
                        annotations_timed_out: 0,
                        speed_traps_hit: 0,
                        average_training_label_agreement: 0.8,
                        average_training_question_overlap_rouge: 0.3,
                        average_training_answer_overlap_rouge: 0.3,
                        average_load_time: 1,
                        average_task_time: 10,
                        average_agreement_with_p3_annotators: 0.7
                    },
                    phase_3: {
                        annotations_done: 5,
                        annotations_assigned: 10,
                        claims_skipped: 1,
                        annotations_timed_out: 0,
                        speed_traps_hit: 0,
                        average_training_label_agreement: 0.8,
                        average_task_time: 3,
                        average_agreement_with_p2_annotators: 0.9
                    }
                })*/
            }
        }).catch((error) => { window.alert(error) })

        var request = {
            method: "get",
            baseURL: config.api_url,
            url: "/global_statistics.php",
            data: {
                logged_in_user_id: localStorage.getItem('user_id'),
                req_type: 'get-statistics'
            }
        };

        axios(request).then((response) => {
            if (response.data.is_admin === false) {
                window.alert("Error: Access denied.")
            } else {
                // Replace with:
                setTaskStats(response.data);

                /*setTaskStats({
                    phase_1: {
                        annotations_done: 5.5,
                        annotations_assigned: 10,
                        claims_skipped: 2.2,
                        annotations_timed_out: 1.1,
                        speed_traps_hit: 0.2,
                        average_training_label_agreement: 0.7,
                        average_training_claim_overlap_rouge: 0.4,
                        average_training_strategy_f1: 0.5,
                        average_training_claim_type_f1: 0.9,
                        average_load_time: 3,
                        average_task_time: 4
                    },
                    phase_2: {
                        annotations_done: 4,
                        annotations_assigned: 10,
                        claims_skipped: 2.2,
                        annotations_timed_out: 1.1,
                        speed_traps_hit: 0.2,
                        average_training_label_agreement: 0.2,
                        average_training_question_overlap_rouge: 0.1,
                        average_training_answer_overlap_rouge: 1.0,
                        average_load_time: 2,
                        average_task_time: 11,
                        average_agreement_with_p3_annotators: 0.6
                    },
                    phase_3: {
                        annotations_done: 7,
                        annotations_assigned: 11,
                        claims_skipped: 2.2,
                        annotations_timed_out: 1.1,
                        speed_traps_hit: 0.2,
                        average_training_label_agreement: 0.9,
                        average_task_time: 12,
                        average_agreement_with_p2_annotators: 0.5
                    }
                })*/
            }
        }).catch((error) => { window.alert(error) })


    }, []);

    let header_text = "User Overview | " + user.username

    return <NoteScreen header={header_text}>
        {user.phase_1 && taskStats.phase_1?
        <UserTaskPerformanceOverview name={"Phase 1"} userStats={user.phase_1} averageStats={taskStats.phase_1} />
        :
        ""
        }
        {user.phase_3 && taskStats.phase_3?
        <UserTaskPerformanceOverview name={"Phase 2"} userStats={user.phase_2} averageStats={taskStats.phase_2} />
        :
        ""
        }
        {user.phase_3 && taskStats.phase_3?
        <UserTaskPerformanceOverview name={"Phase 3"} userStats={user.phase_3} averageStats={taskStats.phase_3} />
        :
        ""
        }
        
    </NoteScreen>;
}