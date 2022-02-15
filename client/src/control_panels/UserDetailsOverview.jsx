import NoteScreen from "../note_screen/NoteScreen";
import React, { useState, useEffect } from 'react';
import UserTaskPerformanceOverview from "./UserTaskPerformanceOverview";

export default function UserDetailsOverview(props) {
    const [user, setUser] = useState({});
    const [taskStats, setTaskStats] = useState({});

    useEffect(() => {
        var userId = new URLSearchParams(window.location.search).get("id")
        console.log("Showing user with ID \'" + userId + "\'.")

        // Todo: get user by id from the database, along with stats.
        // We can compute avgs and rouge here or in backend, which is easiest.
        // Todo: get this only if admin. Else, show access denied error and redirect to /.
        setUser({
            username: "Michael",
            phase_1: {
                annotations_done: 5,
                annotations_assigned: 10,
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
                average_training_label_agreement: 0.8,
                average_task_time: 3,
                average_agreement_with_p2_annotators: 0.9
            }
        })

        setTaskStats({
            phase_1: {
                average_training_label_agreement: 0.7,
                average_training_claim_overlap_rouge: 0.4,
                average_training_strategy_f1: 0.5,
                average_training_claim_type_f1: 0.9,
                average_load_time: 3,
                average_task_time: 4
            },
            phase_2: {
                average_training_label_agreement: 0.2,
                average_training_question_overlap_rouge: 0.1,
                average_training_answer_overlap_rouge: 1.0,
                average_load_time: 2,
                average_task_time: 11,
                average_agreement_with_p3_annotators: 0.6
            },
            phase_3: {
                average_training_label_agreement: 0.9,
                average_task_time: 12,
                average_agreement_with_p2_annotators: 0.5
            }
        })
    }, []);

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

    let header_text = "User Overview | " + user.username

    return <NoteScreen header={header_text}>
        <UserTaskPerformanceOverview name={"Phase 1"} userStats={user.phase_1} averageStats={taskStats.phase_1} chartKeys={chart_keys} />
        <UserTaskPerformanceOverview name={"Phase 2"} userStats={user.phase_2} averageStats={taskStats.phase_2} chartKeys={chart_keys} />
        <UserTaskPerformanceOverview name={"Phase 3"} userStats={user.phase_3} averageStats={taskStats.phase_3} chartKeys={chart_keys} />
    </NoteScreen>;
}