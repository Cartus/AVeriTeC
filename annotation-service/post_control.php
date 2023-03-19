<?php
date_default_timezone_set('UTC');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

// This file provides 5 main functions for training and real annotations. 
// 5 main functions include getting next claim, submitting the current claim, reloading the previous annotated claim,
// resubmitting the reloaded claim and skipping the current claim.
// The logic of these five functions are similar to claim_norm.php

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt =  $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$date = date("Y-m-d H:i:s");

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);


$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['user_id'];
$req_type = $_POST['req_type'];

if ($req_type == "next-data"){

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT user_id, current_post_task FROM Annotators WHERE user_id=?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_post_task'] != 0) {
            $sql = "SELECT * FROM Assigned_Posts WHERE claim_norm_id=?";

            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_post_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $latest=1;
            $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND (edit_latest=? OR p4_latest=?)";
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("iii", $row['claim_qa_id'], $latest, $latest);
            $stmt->execute();
            $result_qa = $stmt->get_result();

            $questions = array();

            $counter = 0;
            if ($result_qa->num_rows > 0) {
                while($row_qa = $result_qa->fetch_assoc()) {
                    $counter = $counter + 1;
                    $count_string = "question_" . (string)$counter;
                    $question_array = array();
                    $question_array['text'] = $row_qa['question'];

                    $answers = array();
                    $answers[0]['answer'] = $row_qa['answer'];
                    $answers[0]['source_url'] = $row_qa['source_url'];
                    $answers[0]['answer_type'] = $row_qa['answer_type'];
                    $answers[0]['source_medium'] = $row_qa['source_medium'];

                    if (!is_null($row_qa["bool_explanation"])){
                        $answers[0]['explanation'] = $row_qa['bool_explanation'];
                    }

                    if (!is_null($row_qa['answer_second'])){
                        $answers[1]['answer'] = $row_qa['answer_second'];
                    }
                    if (!is_null($row_qa['source_url_second'])){
                        $answers[1]['source_url'] = $row_qa['source_url_second'];
                    }
                    if (!is_null($row_qa['answer_type_second'])){
                        $answers[1]['answer_type'] = $row_qa['answer_type_second'];
                    }
                    if (!is_null($row_qa['source_medium_second'])){
                        $answers[1]['source_medium'] = $row_qa['source_medium_second'];
                    }
                    if (!is_null($row_qa["bool_explanation_second"])){
                        $answers[1]['explanation'] = $row_qa['bool_explanation_second'];
                    }

                    if (!is_null($row_qa['answer_third'])){
                        $answers[2]['answer'] = $row_qa['answer_third'];
                    }
                    if (!is_null($row_qa['source_url_third'])){
                        $answers[2]['source_url'] = $row_qa['source_url_third'];
                    }
                    if (!is_null($row_qa['answer_type_third'])){
                        $answers[2]['answer_type'] = $row_qa['answer_type_third'];
                    }
                    if (!is_null($row_qa['source_medium_third'])){
                        $answers[2]['source_medium'] = $row_qa['source_medium_third'];
                    }
                    if (!is_null($row_qa["bool_explanation_third"])){
                        $answers[2]['explanation'] = $row_qa['bool_explanation_third'];
                    }

                    $question_array['answers'] = $answers;
                    $questions[$count_string] = $question_array;
                }

                if (!is_null($row["correction_claim"])){
                    $claim_text = $row['correction_claim'];
                } else {
                    $claim_text = $row['cleaned_claim'];
                }

                $output = (["claim_text" => $claim_text, "claim_speaker" => $row['speaker'], "claim_source" => $row['source'],
                    "claim_date" => $row['check_date'], "claim_hyperlink" => $row['hyperlink'], "questions" => $questions, "country_code" => $row['claim_loc']]);
                echo(json_encode($output));

                update_table($conn, "UPDATE Assigned_Posts SET date_start_post=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);

            } else {
                echo "0 Results";
            }

        } else {
            $sql = "SELECT * FROM Assigned_Posts WHERE user_id_post=? AND post_annotators_num=0 ORDER BY RAND() LIMIT 1";

            $stmt= $conn->prepare($sql);

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if(mysqli_num_rows($result) > 0) {
                $row = $result->fetch_assoc();

                $latest = 1;
                $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND (edit_latest=? OR p4_latest=?)";
                $stmt =  $conn->prepare($sql_qa);
                $stmt->bind_param("iii", $row['claim_qa_id'], $latest, $latest);
                $stmt->execute();
                $result_qa = $stmt->get_result();

                $questions = array();

                $counter = 0;
                if ($result_qa->num_rows > 0) {
                    while($row_qa = $result_qa->fetch_assoc()) {
                        $counter = $counter + 1;
                        $count_string = "question_" . (string)$counter;
                        $question_array = array();

                        $question_array['text'] = $row_qa['question'];
                        $question_array['question_problems'] = explode(" [SEP] ", $row_qa['question_problems']);

                        $answers = array();
                        $answers[0]['answer'] = $row_qa['answer'];
                        $answers[0]['source_url'] = $row_qa['source_url'];
                        $answers[0]['answer_type'] = $row_qa['answer_type'];
                        $answers[0]['source_medium'] = $row_qa['source_medium'];

                        if (!is_null($row_qa["bool_explanation"])){
                            $answers[0]['explanation'] = $row_qa['bool_explanation'];
                        }

                        if (!is_null($row_qa['answer_problems'])){
                            $answers[0]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems']);
                        }

                        if (!is_null($row_qa['answer_second'])){
                            $answers[1]['answer'] = $row_qa['answer_second'];
                        }
                        if (!is_null($row_qa['source_url_second'])){
                            $answers[1]['source_url'] = $row_qa['source_url_second'];
                        }
                        if (!is_null($row_qa['answer_type_second'])){
                            $answers[1]['answer_type'] = $row_qa['answer_type_second'];
                        }
                        if (!is_null($row_qa['source_medium_second'])){
                            $answers[1]['source_medium'] = $row_qa['source_medium_second'];
                        }
                        if (!is_null($row_qa["bool_explanation_second"])){
                            $answers[1]['explanation'] = $row_qa['bool_explanation_second'];
                        }

                        if (!is_null($row_qa['answer_problems_second'])){
                            $answers[1]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_second']);
                        }

                        if (!is_null($row_qa['answer_third'])){
                            $answers[2]['answer'] = $row_qa['answer_third'];
                        }
                        if (!is_null($row_qa['source_url_third'])){
                            $answers[2]['source_url'] = $row_qa['source_url_third'];
                        }
                        if (!is_null($row_qa['answer_type_third'])){
                            $answers[2]['answer_type'] = $row_qa['answer_type_third'];
                        }
                        if (!is_null($row_qa['source_medium_third'])){
                            $answers[2]['source_medium'] = $row_qa['source_medium_third'];
                        }
                        if (!is_null($row_qa["bool_explanation_third"])){
                            $answers[2]['explanation'] = $row_qa['bool_explanation_third'];
                        }

                        if (!is_null($row_qa['answer_problems_third'])){
                            $answers[2]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_third']);
                        }

                        $question_array['answers'] = $answers;
                        $questions[$count_string] = $question_array;
                    }

                    if (!is_null($row["correction_claim"])){
                        $claim_text = $row['correction_claim'];
                    } else {
                        $claim_text = $row['cleaned_claim'];
                    }

                    $output = (["claim_text" => $claim_text, "claim_speaker" => $row['speaker'], "claim_date" => $row['check_date'], "claim_source" => $row['source'],
                    "claim_hyperlink" => $row['hyperlink'], "questions" => $questions, "country_code" => $row['claim_loc']]);
                    echo(json_encode($output));

                } else {
                    echo "0 Results";
                }
                update_table($conn, "UPDATE Assigned_Posts SET date_start_post=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
                update_table($conn, "UPDATE Annotators SET current_post_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
            }
        }
    }
    $conn->close();

} else if ($req_type == "submit-data") {
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Assigned_Posts WHERE (claim_norm_id = (SELECT current_post_task FROM Annotators WHERE user_id=?))";

    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $phase_5_label = $_POST["annotation"]["label"];
    $justification = $_POST["annotation"]["justification"];

    $unreadable = 0;
    if (array_key_exists("unreadable", $_POST["annotation"])) {
        if ($_POST["annotation"]["unreadable"] == 1) {
            $unreadable = 1;
        } else {
            $unreadable = 0;
        }
    } else {
        $unreadable = 0;
    }

    $latest=1;
    $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND (edit_latest=? OR p4_latest=?)";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $row['claim_qa_id'], $latest, $latest);
    $stmt->execute();
    $result_qa = $stmt->get_result();

    $conn->begin_transaction();
    try{
        $counter = 0;
        if ($result_qa->num_rows > 0) {
            while($row_qa = $result_qa->fetch_assoc()) {
                $counter = $counter + 1;
                $count_string = "question_" . (string)$counter;

                if (empty($_POST["questions"][$count_string]["question_problems"])){
                    $question_problems = NULL;
                }else{
                    $question_problems = implode(" [SEP] ", $_POST["questions"][$count_string]["question_problems"]);
                }

                $answers = $_POST["questions"][$count_string]["answers"];
                print_r($_POST["questions"][$count_string]);
                if (array_key_exists('answer_problems', $answers[0])){
                    $answer_problems = implode(" [SEP] ", $answers[0]["answer_problems"]);
                }else{
                    $answer_problems = NULL;
                }

                if (array_key_exists(1, $answers)){
                    if (array_key_exists('answer_problems', $answers[1])){
                        $answer_problems_second = implode(" [SEP] ", $answers[1]["answer_problems"]);
                    }else{
                        $answer_problems_second = NULL;
                    }
                } else {
                    $answer_problems_second = NULL;
                }

                if (array_key_exists(2, $answers)){
                    if (array_key_exists('answer_problems', $answers[2])){
                        $answer_problems_third = implode(" [SEP] ", $answers[2]["answer_problems"]);
                    }else{
                        $answer_problems_third = NULL;
                    }
                } else {
                    $answer_problems_third = NULL;
                }

                update_table($conn, "UPDATE Qapair SET question_problems=?, answer_problems=?, answer_problems_second=?,
                answer_problems_third=? WHERE qa_id=?",'ssssi', $question_problems, $answer_problems, $answer_problems_second, $answer_problems_third, $row_qa['qa_id']);
            }
        }else {
            echo "0 Results";
        }

        $start_time_string = $_POST['startTime'];
        $start_time = date("Y-m-d H:i:s", strtotime($start_time_string));
        $submit_time_string = $_POST['submitTime'];
        $submit_time = date("Y-m-d H:i:s", strtotime($submit_time_string));

        $post_latest = 1;
        update_table($conn, "UPDATE Assigned_Posts SET post_annotators_num=post_annotators_num+1, phase_5_label=?, justification_p5=?, date_made_post=?,
        date_start_post=?, post_latest=? WHERE claim_norm_id=?",'ssssii',
        $phase_5_label, $justification, $submit_time, $start_time, $post_latest, $row['claim_norm_id']);

        $start_time = $_POST['startTime'];
        $submit_time = $_POST['submitTime'];

        $from_time = strtotime($start_time);
        $to_time = strtotime($submit_time);

        $minutes = round(abs($to_time - $from_time) / 60,2);
        echo("The annotation time is: $minutes minutes.");

        $p5_speed_trap = 0;
        if ($minutes < 0.4) {
            $p5_speed_trap = 1;
        }

        update_table($conn, "UPDATE Annotators SET current_post_task=0, finished_post_annotations=finished_post_annotations+1, p5_time_sum=p5_time_sum+?,
        p5_speed_trap=p5_speed_trap+? WHERE user_id=?",'ddi', $minutes, $p5_speed_trap, $user_id);

        $conn->commit();
        echo "Submit Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();

} else if ($req_type == "reload-data") {
    $offset = $_POST['offset'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $post_annotated_num = 0;
    $post_latest = 1;
    $sql = "SELECT * FROM Assigned_Posts WHERE post_latest=? AND user_id_post=? AND post_annotators_num!=?  ORDER BY date_start_post DESC LIMIT 1 OFFSET ?";

    $stmt= $conn->prepare($sql);
    $stmt->bind_param("iiii", $post_latest, $user_id, $post_annotated_num, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $latest = 1;
    $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND (edit_latest=? OR p4_latest=?)";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $row['claim_qa_id'], $latest, $latest);
    $stmt->execute();
    $result_qa = $stmt->get_result();
    
    $questions = array();
    $counter = 0;
    if ($result_qa->num_rows > 0) {
        while($row_qa = $result_qa->fetch_assoc()) {
            $counter = $counter + 1;
            $count_string = "question_" . (string)$counter;
            $question_array = array();
            $question_array['text'] = $row_qa['question'];

            $question_array['question_problems'] = explode(" [SEP] ", $row_qa['question_problems']);

            $answers = array();
            $answers[0]['answer'] = $row_qa['answer'];
            $answers[0]['source_url'] = $row_qa['source_url'];
            $answers[0]['answer_type'] = $row_qa['answer_type'];
            $answers[0]['source_medium'] = $row_qa['source_medium'];

            if (!is_null($row_qa["bool_explanation"])){
                $answers[0]['explanation'] = $row_qa['bool_explanation'];
            }

            if (!is_null($row_qa['answer_problems'])){
                $answers[0]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems']);
            }

            if (!is_null($row_qa['answer_second'])){
                $answers[1]['answer'] = $row_qa['answer_second'];
            }
            if (!is_null($row_qa['source_url_second'])){
                $answers[1]['source_url'] = $row_qa['source_url_second'];
            }
            if (!is_null($row_qa['answer_type_second'])){
                $answers[1]['answer_type'] = $row_qa['answer_type_second'];
            }
            if (!is_null($row_qa['source_medium_second'])){
                $answers[1]['source_medium'] = $row_qa['source_medium_second'];
            }
            if (!is_null($row_qa["bool_explanation_second"])){
                $answers[1]['explanation'] = $row_qa['bool_explanation_second'];
            }

            if (!is_null($row_qa['answer_problems_second'])){
                $answers[1]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_second']);
            }

            if (!is_null($row_qa['answer_third'])){
                $answers[2]['answer'] = $row_qa['answer_third'];
            }
            if (!is_null($row_qa['source_url_third'])){
                $answers[2]['source_url'] = $row_qa['source_url_third'];
            }
            if (!is_null($row_qa['answer_type_third'])){
                $answers[2]['answer_type'] = $row_qa['answer_type_third'];
            }
            if (!is_null($row_qa['source_medium_third'])){
                $answers[2]['source_medium'] = $row_qa['source_medium_third'];
            }
            if (!is_null($row_qa["bool_explanation_third"])){
                $answers[2]['explanation'] = $row_qa['bool_explanation_third'];
            }

            if (!is_null($row_qa['answer_problems_third'])){
                $answers[2]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_third']);
            }

            $question_array['answers'] = $answers;
            $questions[$count_string] = $question_array;
        }
        $annotation = array();
        $annotation['justification'] = $row['justification_p5'];
        $annotation['label'] = $row['phase_5_label'];
        $annotation['unreadable'] = $row['unreadable'];

        if (!is_null($row["correction_claim"])){
            $claim_text = $row['correction_claim'];
        } else {
            $claim_text = $row['cleaned_claim'];
        }

        $output = (["claim_norm_id" => $row['claim_norm_id'], "claim_text" => $claim_text, "speaker" => $row['speaker'], "claim_source" => $row['source'],
        "claim_date" => $row['check_date'], "hyperlink" => $row['hyperlink'],  "questions" => $questions, "annotation" => $annotation, "country_code" => $row['claim_loc']]);
        echo(json_encode($output));

        update_table($conn, "UPDATE Assigned_Posts SET date_restart_post=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);

    } else {
        echo "0 Results";
    }
    $conn->close();
} else if ($req_type == "resubmit-data") {

    print_r($_POST["annotation"]);

    $claim_norm_id = $_POST['claim_norm_id'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Assigned_Posts WHERE claim_norm_id=?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $claim_norm_id);
    $stmt->execute();
    $result_time = $stmt->get_result();
    $row = $result_time->fetch_assoc();

    $post_latest = 0;
    $sql_update = "UPDATE Assigned_Posts SET post_latest=? WHERE claim_norm_id=?";
    $stmt= $conn->prepare($sql_update);
    $stmt->bind_param("ii", $post_latest, $claim_norm_id);
    $stmt->execute();

    $phase_5_label = $_POST["annotation"]["label"];
    $justification = $_POST["annotation"]["justification"];

    $unreadable = 0;
    if (array_key_exists("unreadable", $_POST["annotation"])) {
        if ($_POST["annotation"]["unreadable"] == 1) {
            $unreadable = 1;
        } else {
            $unreadable = 0;
        }
    } else {
        $unreadable = 0;
    }

    $latest = 1;
    $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND (edit_latest=? OR p4_latest=?)";
    $stmt =  $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $row['claim_qa_id'], $latest, $latest);
    $stmt->execute();
    $result_qa = $stmt->get_result();

    $conn->begin_transaction();
    try{
        $counter = 0;
        if ($result_qa->num_rows > 0) {
            while($row_qa = $result_qa->fetch_assoc()) {
                $counter = $counter + 1;
                $count_string = "question_" . (string)$counter;

                if (empty($_POST["questions"][$count_string]["question_problems"])){
                    $question_problems = NULL;
                }else{
                    $question_problems = implode(" [SEP] ", $_POST["questions"][$count_string]["question_problems"]);
                }

                $answers = $_POST["questions"][$count_string]["answers"];
                print_r($_POST["questions"][$count_string]);
                if (array_key_exists('answer_problems', $answers[0])){
                    $answer_problems = implode(" [SEP] ", $answers[0]["answer_problems"]);
                }else{
                    $answer_problems = NULL;
                }

                if (array_key_exists(1, $answers)){
                    if (array_key_exists('answer_problems', $answers[1])){
                        $answer_problems_second = implode(" [SEP] ", $answers[1]["answer_problems"]);
                    }else{
                        $answer_problems_second = NULL;
                    }
                } else {
                    $answer_problems_second = NULL;
                }

                if (array_key_exists(2, $answers)){
                    if (array_key_exists('answer_problems', $answers[2])){
                        $answer_problems_third = implode(" [SEP] ", $answers[2]["answer_problems"]);
                    }else{
                        $answer_problems_third = NULL;
                    }
                } else {
                    $answer_problems_third = NULL;
                }

                update_table($conn, "UPDATE Qapair SET question_problems=?, answer_problems=?, answer_problems_second=?,
                answer_problems_third=? WHERE qa_id=?",'ssssi', $question_problems, $answer_problems, $answer_problems_second, $answer_problems_third, $row_qa['qa_id']);
            }
        }else {
            echo "0 Results";
        }

        $start_time = $_POST['startTime'];
        $submit_time = $_POST['submitTime'];

        $from_time = strtotime($start_time);
        $to_time = strtotime($submit_time);

        $minutes = round(abs($to_time - $from_time) / 60,2);
        echo("The annotation time is: $minutes minutes.");

        update_table($conn, "UPDATE Annotators SET p3_time_sum=p3_time_sum+? WHERE user_id=?",'di', $minutes, $user_id);

        $post_latest = 0;
        update_table($conn, "UPDATE Assigned_Posts SET post_latest=? WHERE claim_norm_id=?",'ii', $post_latest, $claim_norm_id);

        $post_latest = 1;
        $inserted = 0;
        $post_annotators_num = $row['post_annotators_num']+1;

        $start_time_string = $_POST['startTime'];
        $start_time = date("Y-m-d H:i:s", strtotime($start_time_string));
        $submit_time_string = $_POST['submitTime'];
        $submit_time = date("Y-m-d H:i:s", strtotime($submit_time_string));

        update_table($conn, "INSERT INTO Assigned_Posts (claim_id, claim_qa_id, claim_valid_id, claim_dispute_id, web_archive, user_id_norm,
        user_id_qa, user_id_valid,  cleaned_claim, correction_claim, speaker, hyperlink, transcription, media_source, check_date, claim_types, fact_checker_strategy,
        phase_1_label, phase_2_label, qa_annotators_num, qa_skipped, valid_annotators_num, num_qapairs, claim_loc, latest, source, date_start_norm, date_load_norm,
        date_made_norm, date_restart_norm, date_modified_norm, date_start_qa, date_load_qa, date_made_qa, date_restart_qa, date_modified_qa, inserted,
        date_start_valid, date_made_valid, date_restart_valid, date_modified_valid, phase_3_label, phase_4_label, justification, unreadable, valid_latest,
        dispute_annotators_num, added_qas, user_id_dispute, post_annotators_num, user_id_post, date_restart_post, date_modified_post, phase_5_label, justification_p5, post_latest)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        'iiiisiiisssssssssssiiiisisssssssssssisssssssiiiiiiissssi',
        $row['claim_id'], $row['claim_qa_id'], $row['claim_valid_id'], $row['claim_dispute_id'], $row['web_archive'], $row['user_id_norm'],
        $row['user_id_qa'], $row['user_id_valid'], $row['cleaned_claim'], $row['correction_claim'], $row['speaker'], $row['hyperlink'], $row['transcription'], $row['media_source'],
        $row['check_date'], $row['claim_types'], $row['fact_checker_strategy'], $row['phase_1_label'], $row['phase_2_label'], $row['qa_annotators_num'], $row['qa_skipped'],
        $row['valid_annotators_num'], $row['num_qapairs'],  $row['claim_loc'], $row['latest'], $row['source'], $row['date_start_norm'], $row['date_load_norm'],
        $row['date_made_norm'], $row['date_restart_norm'], $row['date_modified_norm'], $row['date_start_qa'], $row['date_load_qa'], $row['date_made_qa'],
        $row['date_restart_qa'], $row['date_modified_qa'], $inserted, $row['date_start_valid'], $row['date_made_valid'], $start_time,
        $row['date_modified_valid'], $row['phase_3_label'], $row['phase_4_label'], $row['justification'], $row['unreadable'], $row['valid_latest'],
        $row['dispute_annotators_num'], $row['added_qas'], $row['user_id_dispute'], $post_annotators_num, $row['user_id_post'], $row['date_restart_post'],
        $submit_time, $phase_5_label, $justification, $post_latest);

        $conn->commit();
        echo "Resubmit Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
}