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


$req_type = $_POST['req_type'];

if ($req_type == "load-data"){
    $offset = $_POST['offset'];
    
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // TODO: gold claim
    $user_id = 1;

    $sql = "SELECT claim_id, skipped FROM QA_Map WHERE user_id=? ORDER BY date_made ASC LIMIT 1 OFFSET ?";
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

    $gold_array = array();

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
    
        array_push($gold_array, ["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
        "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $label, "country_code" => $row['claim_loc'],
        "claim_correction" => $correction_claim, "should_correct" => $should_correct]);
    } 

    $user_id = 2;
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
    
        array_push($gold_array, ["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
        "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $label, "country_code" => $row['claim_loc'],
        "claim_correction" => $correction_claim, "should_correct" => $should_correct]);
    } 

    $gold = (["annotations" => $gold_array]);
    echo(json_encode($gold));
    $conn->close();
}

?>