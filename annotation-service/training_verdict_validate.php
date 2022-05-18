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

$db_params = parse_ini_file( dirname(__FILE__).'/train_params.ini', false);


$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['user_id'];
$req_type = $_POST['req_type'];

if ($req_type == "next-data"){

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT user_id, current_valid_task FROM Annotators WHERE user_id=?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_valid_task'] != 0) {
            $sql = "SELECT claim_norm_id, claim_qa_id, user_id_qa, cleaned_claim, correction_claim, speaker, source, check_date, hyperlink, claim_loc FROM Assigned_Valids WHERE claim_norm_id=?";
            
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_valid_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            $qa_latest=1;
            $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND user_id_qa=? AND qa_latest=?";
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("iii", $row['claim_qa_id'], $row['user_id_qa'], $qa_latest);
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

                update_table($conn, "UPDATE Assigned_Valids SET date_start_valid=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);

            } else {
                echo "0 Results";
            }

        } else {
            $sql = "SELECT claim_norm_id, claim_qa_id, user_id_qa, correction_claim, cleaned_claim, speaker, source, check_date, claim_types, fact_checker_strategy, hyperlink, claim_loc FROM Assigned_Valids
            WHERE user_id_valid=? AND valid_annotators_num=0 ORDER BY RAND() LIMIT 1";

            $stmt= $conn->prepare($sql);

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if(mysqli_num_rows($result) > 0) {
                $row = $result->fetch_assoc();
                
                $qa_latest = 1;
                $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
                $stmt =  $conn->prepare($sql_qa);
                $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $row['user_id_qa']);
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
                update_table($conn, "UPDATE Assigned_Valids SET date_start_valid=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
                update_table($conn, "UPDATE Annotators SET current_valid_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
            }
        }
    }
    $conn->close();

} 