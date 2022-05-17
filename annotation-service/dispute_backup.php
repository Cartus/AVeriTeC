<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

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

    $sql = "SELECT user_id, current_dispute_task, finished_dispute_annotations FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_dispute_task'] != 0) {
            $sql = "SELECT * FROM Assigned_Disputes WHERE claim_norm_id=?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_dispute_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $qa_latest = 1;
            $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $row['user_id_qa']);
            $stmt->execute();
            $result_qa = $stmt->get_result();
    
            $questions = array();
            $counter = 0;
    
            $entries = array();
            while($row_qa = $result_qa->fetch_assoc()) {
                $count_string = "qa_pair_entry_field_" . (string)$counter;
                $counter = $counter + 1;
                $field_array = array();
                $field_array['question'] = $row_qa['question'];
                $field_array['question_problems'] = explode(" [SEP] ", $row_qa['question_problems']);

                $answers = array();
                $answers[0]['answer'] = $row_qa['answer'];
                $answers[0]['source_url'] = $row_qa['source_url'];
                $answers[0]['answer_type'] = $row_qa['answer_type'];
                $answers[0]['source_medium'] = $row_qa['source_medium'];
                $answers[0]['bool_explanation'] = $row_qa['bool_explanation'];
                
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
                if (!is_null($row_qa['bool_explanation_second'])){
                    $answers[1]['bool_explanation'] = $row_qa['bool_explanation_second'];
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
                if (!is_null($row_qa['bool_explanation_third'])){
                    $answers[2]['bool_explanation'] = $row_qa['bool_explanation_third'];
                }
                if (!is_null($row_qa['answer_problems_third'])){
                    $answers[2]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_third']);
                }

                $field_array['answers'] = $answers;
                $entries[$count_string] = $field_array;
            }

            $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id'], "prev_entries" => $entries, 
            "phase_two_label" => $row['phase_2_label'], "phase_three_label" => $row['phase_3_label'], "justification" => $row['justification']]);
            echo(json_encode($output));
            update_table($conn, "UPDATE Assigned_Disputes SET date_start_dispute=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);

        } else {
            $sql = "SELECT * FROM Assigned_Disputes WHERE user_id_dispute=? AND dispute_annotators_num=0 ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $qa_latest = 1;
            $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $row['user_id_qa']);
            $stmt->execute();
            $result_qa = $stmt->get_result();
    
            $questions = array();
            $counter = 0;
    
            $entries = array();
            while($row_qa = $result_qa->fetch_assoc()) {
                $count_string = "qa_pair_entry_field_" . (string)$counter;
                $counter = $counter + 1;
                $field_array = array();
                $field_array['question'] = $row_qa['question'];
                // $field_array['question'] = $row_qa['question_problems'];
                $field_array['question_problems'] = explode(" [SEP] ", $row_qa['question_problems']);

                $answers = array();
                $answers[0]['answer'] = $row_qa['answer'];
                $answers[0]['source_url'] = $row_qa['source_url'];
                $answers[0]['answer_type'] = $row_qa['answer_type'];
                $answers[0]['source_medium'] = $row_qa['source_medium'];
                $answers[0]['bool_explanation'] = $row_qa['bool_explanation'];

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
                if (!is_null($row_qa['bool_explanation_second'])){
                    $answers[1]['bool_explanation'] = $row_qa['bool_explanation_second'];
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
                if (!is_null($row_qa['bool_explanation_third'])){
                    $answers[2]['bool_explanation'] = $row_qa['bool_explanation_third'];
                }
                if (!is_null($row_qa['answer_problems_third'])){
                    $answers[2]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_third']);
                }

                $field_array['answers'] = $answers;
                $entries[$count_string] = $field_array;
            }

            $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id'], "prev_entries" => $entries, 
            "phase_two_label" => $row['phase_2_label'], "phase_three_label" => $row['phase_3_label'], "justification" => $row['justification']]);
            echo(json_encode($output));
            update_table($conn, "UPDATE Assigned_Disputes SET date_start_dispute=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
            update_table($conn, "UPDATE Annotators SET current_dispute_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
        }
    }
    $conn->close();

} else if ($req_type == "submit-data") {
    print_r($_POST);

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Assigned_Disputes WHERE (claim_norm_id = (SELECT current_dispute_task FROM Annotators WHERE user_id=?))";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $phase_4_label = $_POST["qa_pair_footer"]["label"];
    $added_qapairs = $_POST["added_entries"];

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            // print_r($item);
            $question = $item['question'];
            $answers = $item['answers'];

            $answer = $answers[0]['answer'];

            // $question_counter = $question_counter + 1;

            if (array_key_exists('source_url', $answers[0])){
                $source_url = $answers[0]['source_url'];
            }else{
                $source_url = NULL;
            }

            if (array_key_exists('answer_type', $answers[0])){
                $answer_type = $answers[0]['answer_type'];
            }else{
                $answer_type = NULL;
            }

            if (array_key_exists('source_medium', $answers[0])){
                $source_medium = $answers[0]['source_medium'];
            }else{
                $source_medium = NULL;
            }

            if (array_key_exists('bool_explanation', $answers[0])){
                $bool_explanation = $answers[0]['bool_explanation'];
            }else{
                $bool_explanation = NULL;
            }

            if (array_key_exists(1, $answers)){
                $answer_second = $answers[1]['answer'];

                if (array_key_exists('source_url', $answers[1])){
                    $source_url_second = $answers[1]['source_url'];
                }else{
                    $source_url_second = NULL;
                }

                if (array_key_exists('answer_type', $answers[1])){
                    $answer_type_second = $answers[1]['answer_type'];
                }else{
                    $answer_type_second = NULL;
                }

                if (array_key_exists('source_medium', $answers[1])){
                    $source_medium_second = $answers[1]['source_medium'];
                }else{
                    $source_medium_second = NULL;
                }

                if (array_key_exists('bool_explanation', $answers[1])){
                    $bool_explanation_second = $answers[1]['bool_explanation'];
                }else{
                    $bool_explanation_second = NULL;
                }
            }else{
                $answer_second = NULL;
                $source_url_second = NULL;
                $answer_type_second = NULL;
                $source_medium_second = NULL;
                $bool_explanation_second = NULL;
            }

            if (array_key_exists(2, $answers)){
                $answer_third = $answers[2]['answer'];

                if (array_key_exists('source_url', $answers[2])){
                    $source_url_third = $answers[2]['source_url'];
                }else{
                    $source_url_third = NULL;
                }

                if (array_key_exists('answer_type', $answers[2])){
                    $answer_type_third = $answers[2]['answer_type'];
                }else{
                    $answer_type_third = NULL;
                }

                if (array_key_exists('source_medium', $answers[2])){
                    $source_medium_third = $answers[2]['source_medium'];
                }else{
                    $source_medium_third = NULL;
                }

                if (array_key_exists('bool_explanation', $answers[2])){
                    $bool_explanation_third = $answers[2]['bool_explanation'];
                }else{
                    $bool_explanation_third = NULL;
                }
            }else{
                $answer_third = NULL;
                $source_url_third = NULL;
                $answer_type_third = NULL;
                $source_medium_third = NULL;
                $bool_explanation_third = NULL;
            }

            $qa_latest = 1;

            update_table($conn, "INSERT INTO Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium, qa_latest, bool_explanation,
            answer_second, source_url_second, answer_type_second, source_medium_second, bool_explanation_second, answer_third, source_url_third, answer_type_third,
            source_medium_third, bool_explanation_third)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssisssssssssss', $row['claim_qa_id'], $user_id, $question, $answer,
            $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
            $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third);
        }
        
        $added_qas = 1;
        update_table($conn, "UPDATE Assigned_Disputes SET dispute_annotators_num=dispute_annotators_num+1, num_qapairs=num_qapairs+?, p4_num_qapairs=?, phase_4_label=?, date_made_dispute=?,
        date_load_dispute=?, added_qas=? WHERE claim_norm_id=?",'iisssii', 
        $added_qapairs, $added_qapairs, $phase_4_label, $date, $row['date_load_cache_dispute'], $added_qas, $row['claim_norm_id']);

        $to_time = strtotime($date);
        $from_time = strtotime($row['date_start_dispute']);
        $minutes = round(abs($to_time - $from_time) / 60,2);
        echo("The annotation time is: $minutes minutes.");

        $load_time = strtotime($row['date_load_cache_dispute']);
        if ($load_time == 0) {
            $load_minutes = $minutes;
        } else {
            $load_minutes = round(abs($load_time - $from_time) / 60,2);
        }
        echo("The loading time is: $load_minutes minutes.");

        $p4_speed_trap = 0;
        if ($minutes < 0.4) {
            $p4_speed_trap = 1;
        }

        update_table($conn, "UPDATE Annotators SET current_dispute_task=0, finished_dispute_annotations=finished_dispute_annotations+1, p4_time_sum=p4_time_sum+?, p4_load_sum=p4_load_sum+?, 
        p4_speed_trap=p4_speed_trap+?, questions_p4=questions_p4+? 
        WHERE user_id=?",'dddii', $minutes, $load_minutes, $p4_speed_trap, $added_qapairs, $user_id);
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

    $dispute_annotated_num = 0;
    $sql = "SELECT * FROM Assigned_Disputes WHERE user_id_dispute=? AND dispute_annotators_num!=? ORDER BY date_start_dispute DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $dispute_annotated_num, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $qa_latest = 1;
    $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $row['user_id_qa']);
    $stmt->execute();
    $result_qa = $stmt->get_result();
    
    $questions = array();
    $counter = 0;
    
    $prev_entries = array();
    while($row_qa = $result_qa->fetch_assoc()) {
        $count_string = "qa_pair_entry_field_" . (string)$counter;
        $counter = $counter + 1;
        $field_array = array();
        $field_array['question'] = $row_qa['question'];
        $field_array['question_problems'] = explode(" [SEP] ", $row_qa['question_problems']);

        $answers = array();
        $answers[0]['answer'] = $row_qa['answer'];
        $answers[0]['source_url'] = $row_qa['source_url'];
        $answers[0]['answer_type'] = $row_qa['answer_type'];
        $answers[0]['source_medium'] = $row_qa['source_medium'];
        $answers[0]['bool_explanation'] = $row_qa['bool_explanation'];

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
        if (!is_null($row_qa['bool_explanation_second'])){
            $answers[1]['bool_explanation'] = $row_qa['bool_explanation_second'];
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
        if (!is_null($row_qa['bool_explanation_third'])){
            $answers[2]['bool_explanation'] = $row_qa['bool_explanation_third'];
        }
        if (!is_null($row_qa['answer_problems_third'])){
            $answers[2]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_third']);
        }

        $field_array['answers'] = $answers;
        $prev_entries[$count_string] = $field_array;
    }

    $qa_latest = 1;
    $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $user_id);
    $stmt->execute();
    $result_qa = $stmt->get_result();
    
    $questions = array();
    $counter = 0;
    
    $entries = array();
    while($row_qa = $result_qa->fetch_assoc()) {
        $count_string = "qa_pair_entry_field_" . (string)$counter;
        $counter = $counter + 1;
        $field_array = array();
        $field_array['question'] = $row_qa['question'];

        $answers = array();
        $answers[0]['answer'] = $row_qa['answer'];
        $answers[0]['source_url'] = $row_qa['source_url'];
        $answers[0]['answer_type'] = $row_qa['answer_type'];
        $answers[0]['source_medium'] = $row_qa['source_medium'];
        $answers[0]['bool_explanation'] = $row_qa['bool_explanation'];

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
        if (!is_null($row_qa['bool_explanation_second'])){
            $answers[1]['bool_explanation'] = $row_qa['bool_explanation_second'];
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
        if (!is_null($row_qa['bool_explanation_third'])){
            $answers[2]['bool_explanation'] = $row_qa['bool_explanation_third'];
        }

        $field_array['answers'] = $answers;
        $entries[$count_string] = $field_array;
    }

    $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
    "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "prev_entries" => $prev_entries, "entries" => $entries,
    "phase_two_label" => $row['phase_2_label'], "phase_three_label" => $row['phase_3_label'], "justification" => $row['justification'], "label" => $row['phase_4_label']]);
    echo(json_encode($output));
    update_table($conn, "UPDATE Assigned_Disputes SET date_restart_cache_dispute=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);

    $conn->close();

} else if ($req_type == "resubmit-data") {
    $claim_norm_id = $_POST['claim_norm_id'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Assigned_Disputes WHERE claim_norm_id=?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $claim_norm_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $qa_latest = 0;
    $sql_update = "UPDATE Qapair SET qa_latest=? WHERE claim_norm_id=? AND user_id_qa=?";
    $stmt= $conn->prepare($sql_update);
    $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $user_id);
    $stmt->execute();

    $phase_4_label = $_POST["qa_pair_footer"]["label"];
    $added_qapairs = $_POST["added_entries"];

    $question_counter = 0;

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            $question = $item['question'];
            $answers = $item['answers'];

            $answer = $answers[0]['answer'];

            $question_counter = $question_counter + 1;

            if (array_key_exists('source_url', $answers[0])){
                $source_url = $answers[0]['source_url'];
            }else{
                $source_url = NULL;
            }

            if (array_key_exists('answer_type', $answers[0])){
                $answer_type = $answers[0]['answer_type'];
            }else{
                $answer_type = NULL;
            }

            if (array_key_exists('source_medium', $answers[0])){
                $source_medium = $answers[0]['source_medium'];
            }else{
                $source_medium = NULL;
            }

            if (array_key_exists('bool_explanation', $answers[0])){
                $bool_explanation = $answers[0]['bool_explanation'];
            }else{
                $bool_explanation = NULL;
            }

            if (array_key_exists(1, $answers)){
                $answer_second = $answers[1]['answer'];

                if (array_key_exists('source_url', $answers[1])){
                    $source_url_second = $answers[1]['source_url'];
                }else{
                    $source_url_second = NULL;
                }

                if (array_key_exists('answer_type', $answers[1])){
                    $answer_type_second = $answers[1]['answer_type'];
                }else{
                    $answer_type_second = NULL;
                }

                if (array_key_exists('source_medium', $answers[1])){
                    $source_medium_second = $answers[1]['source_medium'];
                }else{
                    $source_medium_second = NULL;
                }

                if (array_key_exists('bool_explanation', $answers[1])){
                    $bool_explanation_second = $answers[1]['bool_explanation'];
                }else{
                    $bool_explanation_second = NULL;
                }
            }else{
                $answer_second = NULL;
                $source_url_second = NULL;
                $answer_type_second = NULL;
                $source_medium_second = NULL;
                $bool_explanation_second = NULL;
            }

            if (array_key_exists(2, $answers)){
                $answer_third = $answers[2]['answer'];

                if (array_key_exists('source_url', $answers[2])){
                    $source_url_third = $answers[2]['source_url'];
                }else{
                    $source_url_third = NULL;
                }

                if (array_key_exists('answer_type', $answers[2])){
                    $answer_type_third = $answers[2]['answer_type'];
                }else{
                    $answer_type_third = NULL;
                }

                if (array_key_exists('source_medium', $answers[2])){
                    $source_medium_third = $answers[2]['source_medium'];
                }else{
                    $source_medium_third = NULL;
                }

                if (array_key_exists('bool_explanation', $answers[2])){
                    $bool_explanation_third = $answers[2]['bool_explanation'];
                }else{
                    $bool_explanation_third = NULL;
                }
            }else{
                $answer_third = NULL;
                $source_url_third = NULL;
                $answer_type_third = NULL;
                $source_medium_third = NULL;
                $bool_explanation_third = NULL;
            }

            $qa_latest = 1;

            update_table($conn, "INSERT INTO Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium, qa_latest, bool_explanation,
            answer_second, source_url_second, answer_type_second, source_medium_second, bool_explanation_second, answer_third, source_url_third, answer_type_third, source_medium_third, bool_explanation_third)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssisssssssssss', $row['claim_qa_id'], $user_id, $question, $answer,
            $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
            $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third);
        }

        $added_qas = 1;
        $resulted_qapairs = $added_qapairs - $row['p4_num_qapairs'];
        update_table($conn, "UPDATE Assigned_Disputes SET dispute_annotators_num=dispute_annotators_num+1, num_qapairs=num_qapairs+?, p4_num_qapairs=p4_num_qapairs+?,
        phase_4_label=?, date_modified_dispute=?, date_restart_dispute=?, date_load_dispute=?, added_qas=? WHERE claim_norm_id=?",'iissssii', 
        $resulted_qapairs, $resulted_qapairs, $phase_4_label, $date, $row['date_restart_cache_dispute'], $row['date_load_cache_dispute'], $added_qas, $claim_norm_id);

        $to_time = strtotime($date);
        $from_time = strtotime($row['date_restart_cache_dispute']);
        $minutes = round(abs($to_time - $from_time) / 60,2);
        echo("The annotation time is: $minutes minutes.");

        $load_time = strtotime($row['date_load_cache_dispute']);
        $load_minutes = round(abs($load_time - $from_time) / 60,2);
        echo("The loading time is: $load_minutes minutes.");

        update_table($conn, "UPDATE Annotators SET p4_time_sum=p4_time_sum+?, p4_load_sum=p4_load_sum+?, questions_p4=questions_p4+?
        WHERE user_id=?",'ddii', $minutes, $load_minutes, $added_qapairs, $user_id);

        $conn->commit();
        echo "Resubmit Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
} 

?>
