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

    $gold_array = array();
    
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = 1;

    $sql = "SELECT * FROM VV_Map WHERE user_id=? ORDER BY date_made DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row_map = $result->fetch_assoc();

    $justification = $row_map['justification'];
    $label = $row_map['phase_3_label'];
    $unreadable = $row_map['unreadable'];

    $sql_norm = "SELECT * FROM Train_Norm_Claims WHERE claim_norm_id=?";
    $stmt = $conn->prepare($sql_norm);
    $stmt->bind_param("i", $row_map['claim_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $qa_latest = 1;
    $user_id_qa = 1;
    $sql_qa = "SELECT * FROM Train_Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $user_id_qa);
    $stmt->execute();
    $result_qa = $stmt->get_result();

    $questions = array();
    $counter = 0;
    if ($result_qa->num_rows > 0) {
        while($row_qa = $result_qa->fetch_assoc()) {

            $correction_claim = $row_qa['correction_claim'];

            $counter = $counter + 1;
            $count_string = "question_" . (string)$counter;
            $question_array = array();
            $question_array['text'] = $row_qa['question'];

            $sql_problem = "SELECT * FROM Qaproblem WHERE qa_id=?";
            $stmt = $conn->prepare($sql_problem);
            $stmt->bind_param("i", $row_qa['qa_id']);
            $stmt->execute();
            $result_problem = $stmt->get_result();
            $row_problem = $result_problem->fetch_assoc();

            $question_array['question_problems'] = explode(" [SEP] ", $row_problem['question_problems']);

            $answers = array();
            $answers[0]['answer'] = $row_qa['answer'];
            $answers[0]['source_url'] = $row_qa['source_url'];
            $answers[0]['answer_type'] = $row_qa['answer_type'];
            $answers[0]['source_medium'] = $row_qa['source_medium'];

            if (!is_null($row_qa["bool_explanation"])){
                $answers[0]['explanation'] = $row_qa['bool_explanation'];
            }

            if (!is_null($row_problem['answer_problems'])){
                $answers[0]['answer_problems'] = explode(" [SEP] ", $row_problem['answer_problems']);
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

            if (!is_null($row_problem['answer_problems_second'])){
                $answers[1]['answer_problems'] = explode(" [SEP] ", $row_problem['answer_problems_second']);
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

            if (!is_null($row_problem['answer_problems_third'])){
                $answers[2]['answer_problems'] = explode(" [SEP] ", $row_problem['answer_problems_third']);
            }

            $question_array['answers'] = $answers;
            $questions[$count_string] = $question_array;
        }
        $annotation = array();
        $annotation['justification'] = $row_map['justification'];
        $annotation['label'] = $row_map['phase_3_label'];
        $annotation['unreadable'] = $row_map['unreadable'];

        if (!is_null($correction_claim)){
            $claim_text = $correction_claim;
        } else {
            $claim_text = $row['cleaned_claim'];
        }

        array_push($gold_array, ["claim_norm_id" => $row['claim_norm_id'], "claim_text" => $claim_text, "speaker" => $row['speaker'], "claim_source" => $row['source'],
        "claim_date" => $row['check_date'], "hyperlink" => $row['hyperlink'],  "questions" => $questions, "annotation" => $annotation, "country_code" => $row['claim_loc']]);

    } else {
        echo "0 Results";
    }

    $user_id = 2;

    $sql = "SELECT * FROM VV_Map WHERE user_id=? ORDER BY date_made DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row_map = $result->fetch_assoc();

    $justification = $row_map['justification'];
    $label = $row_map['phase_3_label'];
    $unreadable = $row_map['unreadable'];

    $sql_norm = "SELECT * FROM Train_Norm_Claims WHERE claim_norm_id=?";
    $stmt = $conn->prepare($sql_norm);
    $stmt->bind_param("i", $row_map['claim_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $qa_latest = 1;
    $user_id_qa = 1;
    $sql_qa = "SELECT * FROM Train_Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $user_id_qa);
    $stmt->execute();
    $result_qa = $stmt->get_result();

    $questions = array();
    $counter = 0;
    if ($result_qa->num_rows > 0) {
        while($row_qa = $result_qa->fetch_assoc()) {

            $correction_claim = $row_qa['correction_claim'];

            $counter = $counter + 1;
            $count_string = "question_" . (string)$counter;
            $question_array = array();
            $question_array['text'] = $row_qa['question'];

            $sql_problem = "SELECT * FROM Qaproblem WHERE qa_id=?";
            $stmt = $conn->prepare($sql_problem);
            $stmt->bind_param("i", $row_qa['qa_id']);
            $stmt->execute();
            $result_problem = $stmt->get_result();
            $row_problem = $result_problem->fetch_assoc();

            $question_array['question_problems'] = explode(" [SEP] ", $row_problem['question_problems']);

            $answers = array();
            $answers[0]['answer'] = $row_qa['answer'];
            $answers[0]['source_url'] = $row_qa['source_url'];
            $answers[0]['answer_type'] = $row_qa['answer_type'];
            $answers[0]['source_medium'] = $row_qa['source_medium'];

            if (!is_null($row_qa["bool_explanation"])){
                $answers[0]['explanation'] = $row_qa['bool_explanation'];
            }

            if (!is_null($row_problem['answer_problems'])){
                $answers[0]['answer_problems'] = explode(" [SEP] ", $row_problem['answer_problems']);
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

            if (!is_null($row_problem['answer_problems_second'])){
                $answers[1]['answer_problems'] = explode(" [SEP] ", $row_problem['answer_problems_second']);
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

            if (!is_null($row_problem['answer_problems_third'])){
                $answers[2]['answer_problems'] = explode(" [SEP] ", $row_problem['answer_problems_third']);
            }

            $question_array['answers'] = $answers;
            $questions[$count_string] = $question_array;
        }
        $annotation = array();
        $annotation['justification'] = $row_map['justification'];
        $annotation['label'] = $row_map['phase_3_label'];
        $annotation['unreadable'] = $row_map['unreadable'];

        if (!is_null($correction_claim)){
            $claim_text = $correction_claim;
        } else {
            $claim_text = $row['cleaned_claim'];
        }

        array_push($gold_array, ["claim_norm_id" => $row['claim_norm_id'], "claim_text" => $claim_text, "speaker" => $row['speaker'], "claim_source" => $row['source'],
        "claim_date" => $row['check_date'], "hyperlink" => $row['hyperlink'],  "questions" => $questions, "annotation" => $annotation, "country_code" => $row['claim_loc']]);

    } else {
        echo "0 Results";
    }

    $gold = (["annotations" => $gold_array]);
    echo(json_encode($gold));
    $conn->close();

} 