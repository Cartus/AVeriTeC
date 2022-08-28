<?php
date_default_timezone_set('UTC');
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

$is_train = $_POST['dataset'];
$user_id = $_POST['user_id'];
$req_type = $_POST['req_type'];

if ($is_train == "training") {
    if ($req_type == "next-data"){

        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT * FROM Annotators WHERE user_id=?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $row = $result->fetch_assoc();
    
        if($result->num_rows > 0){
            if ($row['train_current_qa_task'] != 0) {
                $sql = "SELECT * FROM Train_Norm_Claims WHERE claim_norm_id=?";
                $stmt= $conn->prepare($sql);
                $stmt->bind_param("i", $row['train_current_qa_task']);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id']]);
                echo(json_encode($output));

            } else {
                // TODO: update the id here
                $user_id_norm = 2;

                $sql = "SELECT * FROM Train_Norm_Claims WHERE user_id_norm=? AND 
                latest=1 AND Train_Norm_Claims.claim_norm_id NOT IN (SELECT QA_Map.claim_id FROM QA_Map WHERE user_id=?)";
                
                $stmt= $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id_norm, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
    
                $conn->begin_transaction();
                try {
                    if(mysqli_num_rows($result) > 0) {
                        $row = $result->fetch_assoc();
                        $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                        "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id'], "user_id_norm" => $row['user_id_norm']]);
                        echo(json_encode($output));
    
                        update_table($conn, "UPDATE Annotators SET train_current_qa_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
                        $conn->commit();
                    }
                }catch (mysqli_sql_exception $exception) {
                    $conn->rollback();
                    throw $exception;
                }
            }
        }
        $conn->close();
    
    } else if ($req_type == "submit-data") {
        print_r($_POST);
    
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT * FROM Train_Norm_Claims WHERE (claim_norm_id = (SELECT train_current_qa_task FROM Annotators WHERE user_id=?))";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $phase_2_label = $_POST["qa_pair_footer"]["label"];
        $num_qapairs = $_POST["added_entries"];
    
        if (array_key_exists('qa_pair_header', $_POST)){
            if (array_key_exists('claim_correction', $_POST['qa_pair_header'])){
                $correction_claim = $_POST['qa_pair_header']['claim_correction'];
            }else{
                $correction_claim = NULL;
            }
        }else{
            $correction_claim = NULL;
        }
    
        // $question_counter = 0;
    
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
    
                update_table($conn, "INSERT INTO Train_Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium, qa_latest, bool_explanation,
                answer_second, source_url_second, answer_type_second, source_medium_second, bool_explanation_second, answer_third, source_url_third, answer_type_third,
                source_medium_third, bool_explanation_third, phase_2_label, correction_claim)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssisssssssssssss', $row['claim_norm_id'], $user_id, $question, $answer,
                $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
                $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third,
                $phase_2_label, $correction_claim);

            }
            
            $skipped = 0;
            update_table($conn, "INSERT INTO QA_Map (user_id, claim_id, skipped, date_made) VALUES (?, ?, ?, ?)", 'iiis', $user_id, $row['claim_norm_id'], $skipped, $date);

            update_table($conn, "UPDATE Annotators SET train_current_qa_task=0, train_finished_qa_annotations=train_finished_qa_annotations+1 WHERE user_id=?",'i', $user_id);

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

        $sql = "SELECT claim_id, skipped FROM QA_Map WHERE user_id=? ORDER BY date_made DESC LIMIT 1 OFFSET ?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $row_map = $result->fetch_assoc();
    
        $sql_norm = "SELECT * FROM Train_Norm_Claims WHERE claim_norm_id=?";
        $stmt = $conn->prepare($sql_norm);
        $stmt->bind_param("i", $row_map['claim_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if ($row_map['skipped'] == 1) {
            $entries = array();
    
            $field_array['question'] = NULL;
    
            $answers = array();
            $answers[0]['answer'] = NULL;
            $answers[0]['source_url'] = NULL;
            $answers[0]['answer_type'] = NULL;
            $answers[0]['source_medium'] = NULL;
    
            $field_array['answers'] = $answers;
            $entries["qa_pair_entry_field_0"] = $field_array;
    
            $should_correct = 0;
    
            $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $row['phase_2_label'], "country_code" => $row['claim_loc'],
                "claim_correction" => $row['correction_claim'], "should_correct" => $should_correct]);
            echo(json_encode($output));
            $conn->close();
        } else {
            $qa_latest = 1;
            $sql_qa = "SELECT * FROM Train_Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $user_id);
            $stmt->execute();
            $result_qa = $stmt->get_result();
    
            $questions = array();
            $counter = 0;
    
            $entries = array();
            if ($result_qa->num_rows > 0) {
                while($row_qa = $result_qa->fetch_assoc()) {
                    $label = $row_qa['phase_2_label'];
                    $correction_claim = $row_qa['correction_claim'];
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
                
                if (is_null($correction_claim)){
                    $should_correct = 0;
                } else {
                    $should_correct = 1;
                }
    
                $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $label, "country_code" => $row['claim_loc'],
                "claim_correction" => $correction_claim, "should_correct" => $should_correct]);
                echo(json_encode($output));
            } else {
                echo "0 Results";
            }
            $conn->close();
        }
    } else if ($req_type == "resubmit-data") {
        $claim_norm_id = $_POST['claim_norm_id'];
    
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $qa_latest = 0;
        $sql_update = "UPDATE Train_Qapair SET qa_latest=? WHERE claim_norm_id=? AND user_id_qa=?";
        $stmt= $conn->prepare($sql_update);
        $stmt->bind_param("iii", $qa_latest, $claim_norm_id, $user_id);
        $stmt->execute();
    
        $sql = "SELECT * FROM Train_Norm_Claims WHERE claim_norm_id=?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $claim_norm_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $phase_2_label = $_POST["qa_pair_footer"]["label"];
        $num_qapairs = $_POST["added_entries"];
    
        if (array_key_exists('qa_pair_header', $_POST)){
            if (array_key_exists('claim_correction', $_POST['qa_pair_header'])){
                $correction_claim = $_POST['qa_pair_header']['claim_correction'];
            }else{
                $correction_claim = NULL;
            }
        }else{
            $correction_claim = NULL;
        }
    
        // $question_counter = 0;
    
        $conn->begin_transaction();
        try {
            foreach($_POST['entries'] as $item) {
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
    
                update_table($conn, "INSERT INTO Train_Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium, qa_latest, bool_explanation,
                answer_second, source_url_second, answer_type_second, source_medium_second, bool_explanation_second, answer_third, source_url_third, answer_type_third, source_medium_third, 
                bool_explanation_third, phase_2_label, correction_claim)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssisssssssssssss', $claim_norm_id, $user_id, $question, $answer,
                $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
                $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third, $phase_2_label, $correction_claim);
        
            }
            update_table($conn, "UPDATE QA_Map SET skipped=0, date_modified=? WHERE user_id=? AND claim_id=?", 'sii', $date, $user_id, $claim_norm_id);
        
            $conn->commit();
            echo "Resubmit Successfully!";
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    } else if ($req_type == "skip-data") {
    
        $claim_norm_id = $_POST['claim_norm_id'];
    
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $skipped = 1;
    
        $conn->begin_transaction();
        try {
            update_table($conn, "INSERT INTO QA_Map (user_id, claim_id, skipped, date_made) VALUES (?, ?, ?, ?)", 'iiis', $user_id, $claim_norm_id, $skipped, $date);
            update_table($conn, "UPDATE Annotators SET train_current_qa_task=0, train_finished_qa_annotations=train_finished_qa_annotations+1 WHERE user_id=?",'i', $user_id);
            $conn->commit();
            echo "Skip Successfully!";
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    }
    
} else {
    // For real annotations.
    if ($req_type == "next-data"){

        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT user_id, current_qa_task, finished_qa_annotations FROM Annotators WHERE user_id = ?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $row = $result->fetch_assoc();
    
        if($result->num_rows > 0){
            if ($row['current_qa_task'] != 0) {
                $sql = "SELECT * FROM Assigned_Norms WHERE claim_norm_id=?";
                $stmt= $conn->prepare($sql);
                $stmt->bind_param("i", $row['current_qa_task']);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                $sql_claim = "SELECT claim_id FROM Norm_Claims WHERE claim_norm_id=?";
                $stmt= $conn->prepare($sql_claim);
                $stmt->bind_param("i", $row['claim_id']);
                $stmt->execute();
                $result_claim = $stmt->get_result();
                $row_claim = $result_claim->fetch_assoc();

                $sql_text = "SELECT cleaned_claim FROM Norm_Claims WHERE claim_id=? AND claim_norm_id != ?";
                $stmt= $conn->prepare($sql_text);
                $stmt->bind_param("ii", $row_claim['claim_id'], $row['claim_id']);
                $stmt->execute();
                $result_text = $stmt->get_result();

                $text_array = array();

                if ($result_text->num_rows > 0) {
                    while($row_text = $result_text->fetch_assoc()) {
                        array_push($text_array, $row_text['cleaned_claim']);
                    }
                }

                $output = (["web_archive" => $row['web_archive'], "other_extracted_claims" => $text_array, "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id']]);
                echo(json_encode($output));
                update_table($conn, "UPDATE Assigned_Norms SET date_start_qa=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
    
            } else {
                $sql = "SELECT * FROM Assigned_Norms WHERE user_id_qa=? AND qa_annotators_num=0 AND qa_skipped=0 ORDER BY RAND() LIMIT 1";
                $stmt= $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
    
                $conn->begin_transaction();
                try {
                    if(mysqli_num_rows($result) > 0) {
                        $row = $result->fetch_assoc();

                        $sql_claim = "SELECT claim_id FROM Norm_Claims WHERE claim_norm_id=?";
                        $stmt= $conn->prepare($sql_claim);
                        $stmt->bind_param("i", $row['claim_id']);
                        $stmt->execute();
                        $result_claim = $stmt->get_result();
                        $row_claim = $result_claim->fetch_assoc();

                        $sql_text = "SELECT cleaned_claim FROM Norm_Claims WHERE claim_id=? AND claim_norm_id != ?";
                        $stmt= $conn->prepare($sql_text);
                        $stmt->bind_param("ii", $row_claim['claim_id'], $row['claim_id']);
                        $stmt->execute();
                        $result_text = $stmt->get_result();

                        $text_array = array();

                        if ($result_text->num_rows > 0) {
                            while($row_text = $result_text->fetch_assoc()) {
                                array_push($text_array, $row_text['cleaned_claim']);
                            }
                        }

                        $output = (["web_archive" => $row['web_archive'], "other_extracted_claims" => $text_array, "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                        "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id'], "user_id_norm" => $row['user_id_norm']]);
                        echo(json_encode($output));
    
                        update_table($conn, "UPDATE Assigned_Norms SET date_start_qa=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
                        update_table($conn, "UPDATE Annotators SET current_qa_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
                        $conn->commit();
                    }
                }catch (mysqli_sql_exception $exception) {
                    $conn->rollback();
                    throw $exception;
                }
            }
        }
        $conn->close();
    
    } else if ($req_type == "submit-data") {
        print_r($_POST);
    
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT claim_norm_id, date_start_qa, date_load_cache_qa FROM Assigned_Norms WHERE (claim_norm_id = (SELECT current_qa_task FROM Annotators WHERE user_id=?))";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $start_time_string = $_POST['startTime'];
        $start_time = date("Y-m-d H:i:s", strtotime($start_time_string));
        $submit_time_string = $_POST['submitTime'];
        $submit_time = date("Y-m-d H:i:s", strtotime($submit_time_string));

        $from_time = strtotime($start_time);
        $load_time = strtotime($row['date_load_cache_qa']);

        if ($from_time > $load_time) {
            $load_time = NULL;
        } else {
            $load_time = $row['date_load_cache_qa'];
        }
    
        $phase_2_label = $_POST["qa_pair_footer"]["label"];
        $num_qapairs = $_POST["added_entries"];
    
        if (array_key_exists('qa_pair_header', $_POST)){
            if (array_key_exists('claim_correction', $_POST['qa_pair_header'])){
                $correction_claim = $_POST['qa_pair_header']['claim_correction'];
            }else{
                $correction_claim = NULL;
            }
        }else{
            $correction_claim = NULL;
        }
    
        // $question_counter = 0;
    
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
                    update_table($conn, "INSERT INTO Cache (link) VALUES (?)", 's', $source_url);
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
                        update_table($conn, "INSERT INTO Cache (link) VALUES (?)", 's', $source_url_second);
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
                        update_table($conn, "INSERT INTO Cache (link) VALUES (?)", 's', $source_url_third);
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
                source_medium_third, bool_explanation_third, date_start, date_load, date_made)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssissssssssssssss', $row['claim_norm_id'], $user_id, $question, $answer,
                $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
                $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third,
                $start_time, $load_time, $submit_time);
            }
            
            update_table($conn, "UPDATE Assigned_Norms SET qa_annotators_num=qa_annotators_num+1, phase_2_label=?, num_qapairs=?, date_made_qa=?,
            correction_claim=?, date_load_qa=? WHERE claim_norm_id=?",'sisssi', $phase_2_label, $num_qapairs, $date, $correction_claim, $row['date_load_cache_qa'], $row['claim_norm_id']);

            $from_time = strtotime($start_time);
            $to_time = strtotime($submit_time);

            $minutes = round(abs($to_time - $from_time) / 60,2);
            echo("The annotation time is: $minutes minutes.");

            $load_time = strtotime($load_time);
            if (empty($load_time)) {
                $load_minutes = $minutes;
            } else {
                if ($from_time > $load_time) {
                    $load_minutes = $minutes;
                } else {
                    $load_minutes = round(abs($load_time - $from_time) / 60,2);
                }
            }
            echo("The loading time is: $load_minutes minutes.");
    
            $p2_speed_trap = 0;
            if ($minutes < 0.4) {
                $p2_speed_trap = 1;
            }
    
            update_table($conn, "UPDATE Annotators SET current_qa_task=0, finished_qa_annotations=finished_qa_annotations+1, p2_time_sum=p2_time_sum+?, p2_load_sum=p2_load_sum+?, 
            p2_speed_trap=p2_speed_trap+?, questions_p2=questions_p2+? 
            WHERE user_id=?",'dddii', $minutes, $load_minutes, $p2_speed_trap, $num_qapairs, $user_id);
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
    
        $qa_annotated_num = 0;
        $sql = "SELECT * FROM Assigned_Norms WHERE user_id_qa=? AND qa_annotators_num!=? ORDER BY date_start_qa DESC LIMIT 1 OFFSET ?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $qa_annotated_num, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $sql_claim = "SELECT claim_id FROM Norm_Claims WHERE claim_norm_id=?";
        $stmt= $conn->prepare($sql_claim);
        $stmt->bind_param("i", $row['claim_id']);
        $stmt->execute();
        $result_claim = $stmt->get_result();
        $row_claim = $result_claim->fetch_assoc();

        $sql_text = "SELECT cleaned_claim FROM Norm_Claims WHERE claim_id=? AND claim_norm_id != ?";
        $stmt= $conn->prepare($sql_text);
        $stmt->bind_param("ii", $row_claim['claim_id'], $row['claim_id']);
        $stmt->execute();
        $result_text = $stmt->get_result();

        $text_array = array();

        if ($result_text->num_rows > 0) {
            while($row_text = $result_text->fetch_assoc()) {
                array_push($text_array, $row_text['cleaned_claim']);
            }
        }
    
        if ($row['qa_skipped'] == 1) {
            $entries = array();
    
            $field_array['question'] = NULL;
    
            $answers = array();
            $answers[0]['answer'] = NULL;
            $answers[0]['source_url'] = NULL;
            $answers[0]['answer_type'] = NULL;
            $answers[0]['source_medium'] = NULL;
    
            $field_array['answers'] = $answers;
            $entries["qa_pair_entry_field_0"] = $field_array;
    
            $should_correct = 0;
    
            $output = (["claim_norm_id" => $row['claim_norm_id'], "other_extracted_claims" => $text_array, "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $row['phase_2_label'], "country_code" => $row['claim_loc'],
                "claim_correction" => $row['correction_claim'], "should_correct" => $should_correct]);
            echo(json_encode($output));
            update_table($conn, "UPDATE Assigned_Norms SET date_restart_cache_qa=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
            $conn->close();
        } else {
            $qa_latest = 1;
            $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $user_id);
            $stmt->execute();
            $result_qa = $stmt->get_result();
    
            $questions = array();
            $counter = 0;
    
            $entries = array();
            if ($result_qa->num_rows > 0) {
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
    
                if (empty($row['correction_claim'])){
                    $should_correct = 0;
                } else {
                    $should_correct = 1;
                }
    
                $output = (["claim_norm_id" => $row['claim_norm_id'], "other_extracted_claims" => $text_array, "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $row['phase_2_label'], "country_code" => $row['claim_loc'],
                "claim_correction" => $row['correction_claim'], "should_correct" => $should_correct]);
                echo(json_encode($output));
                update_table($conn, "UPDATE Assigned_Norms SET date_restart_cache_qa=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
            } else {
                echo "0 Results";
            }
            $conn->close();
        }
    } else if ($req_type == "resubmit-data") {
        $claim_norm_id = $_POST['claim_norm_id'];
    
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $qa_latest = 0;
        $sql_update = "UPDATE Qapair SET qa_latest=? WHERE claim_norm_id=? AND user_id_qa=?";
        $stmt= $conn->prepare($sql_update);
        $stmt->bind_param("iii", $qa_latest, $claim_norm_id, $user_id);
        $stmt->execute();
    
        $sql = "SELECT * FROM Assigned_Norms WHERE claim_norm_id=?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $claim_norm_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $start_time_string = $_POST['startTime'];
        $start_time = date("Y-m-d H:i:s", strtotime($start_time_string));
        $submit_time_string = $_POST['submitTime'];
        $submit_time = date("Y-m-d H:i:s", strtotime($submit_time_string));

        $from_time = strtotime($start_time);
        $load_time = strtotime($row['date_load_cache_qa']);

        if ($from_time > $load_time) {
            $load_time = NULL;
        } else {
            $load_time = $row['date_load_cache_qa'];
        }
    
        $phase_2_label = $_POST["qa_pair_footer"]["label"];
        $num_qapairs = $_POST["added_entries"];
    
        if (array_key_exists('qa_pair_header', $_POST)){
            if (array_key_exists('claim_correction', $_POST['qa_pair_header'])){
                $correction_claim = $_POST['qa_pair_header']['claim_correction'];
            }else{
                $correction_claim = NULL;
            }
        }else{
            $correction_claim = NULL;
        }
    
        // $question_counter = 0;
    
        $conn->begin_transaction();
        try {
            foreach($_POST['entries'] as $item) {
                $question = $item['question'];
                $answers = $item['answers'];
    
                $answer = $answers[0]['answer'];
    
                // $question_counter = $question_counter + 1;
    
                if (array_key_exists('source_url', $answers[0])){
                    $source_url = $answers[0]['source_url'];
                    update_table($conn, "INSERT INTO Cache (link) VALUES (?)", 's', $source_url);
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
                        update_table($conn, "INSERT INTO Cache (link) VALUES (?)", 's', $source_url_second);
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
                        update_table($conn, "INSERT INTO Cache (link) VALUES (?)", 's', $source_url_third);
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
                source_medium_third, bool_explanation_third, date_start, date_load, date_made)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssissssssssssssss', $claim_norm_id, $user_id, $question, $answer,
                $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
                $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third,
                $start_time, $load_time, $submit_time);
            }

            update_table($conn, "UPDATE Assigned_Norms SET qa_skipped=0, qa_annotators_num=qa_annotators_num+1, phase_2_label=?,
            num_qapairs=?, date_modified_qa=?, correction_claim=?, date_restart_qa=?, date_load_qa=? WHERE claim_norm_id=?",'sissssi',
            $phase_2_label, $num_qapairs, $date, $correction_claim, $start_time, $load_time, $claim_norm_id);

            $from_time = strtotime($start_time);
            $to_time = strtotime($submit_time);

            $minutes = round(abs($to_time - $from_time) / 60,2);
            echo("The annotation time is: $minutes minutes.");

            $load_time = strtotime($load_time);
            if (empty($load_time)) {
                $load_minutes = $minutes;
            } else {
                if ($from_time > $load_time) {
                    $load_minutes = $minutes;
                } else {
                    $load_minutes = round(abs($load_time - $from_time) / 60,2);
                }
            }
            echo("The loading time is: $load_minutes minutes.");
    
            update_table($conn, "UPDATE Annotators SET p2_time_sum=p2_time_sum+?, p2_load_sum=p2_load_sum+?, questions_p2=questions_p2+?
            WHERE user_id=?",'ddii', $minutes, $load_minutes, $num_qapairs, $user_id);
    
            $conn->commit();
            echo "Resubmit Successfully!";
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    } else if ($req_type == "skip-data") {
    
        $claim_norm_id = $_POST['claim_norm_id'];
        $reason = $_POST['skip_reason'];
    
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT date_load_cache_qa FROM Assigned_Norms WHERE (claim_norm_id = (SELECT current_qa_task FROM Annotators WHERE user_id=?))";
    
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        if(empty($row['date_load_cache_qa'])){
            update_table($conn, "UPDATE Annotators SET p2_timed_out=p2_timed_out+1 WHERE user_id=?", 'i', $user_id);
        }
    
        $conn->begin_transaction();
        try {
            update_table($conn, "UPDATE Assigned_Norms SET qa_annotators_num=1, qa_skipped=1, skipped_reason=?, qa_skipped_by=? WHERE claim_norm_id=?",'sii', $reason, $user_id, $claim_norm_id);
            update_table($conn, "UPDATE Annotators SET current_qa_task=0, skipped_qa_data=skipped_qa_data+1, finished_qa_annotations=finished_qa_annotations+1 WHERE user_id=?",'i', $user_id);
            $conn->commit();
            echo "Skip Successfully!";
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    }
    
}


?>
