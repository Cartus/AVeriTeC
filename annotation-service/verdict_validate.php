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
            if ($row['train_current_valid_task'] != 0) {
                $sql = "SELECT * FROM Train_Norm_Claims WHERE claim_norm_id=?";
                
                $stmt= $conn->prepare($sql);
                $stmt->bind_param("i", $row['train_current_valid_task']);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                $user_id_qa=1;

                $qa_latest=1;
                $sql_qa = "SELECT * FROM Train_Qapair WHERE claim_norm_id=? AND user_id_qa=? AND qa_latest=?";
                $stmt = $conn->prepare($sql_qa);
                $stmt->bind_param("iii", $row['claim_norm_id'], $user_id_qa, $qa_latest);
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
    
                    if (!is_null($correction_claim)){
                        $claim_text = $correction_claim;
                    } else {
                        $claim_text = $row['cleaned_claim'];
                    }
    
                    $output = (["claim_text" => $claim_text, "claim_speaker" => $row['speaker'], "claim_source" => $row['source'],
                        "claim_date" => $row['check_date'], "claim_hyperlink" => $row['hyperlink'], "questions" => $questions, "country_code" => $row['claim_loc']]);
                    echo(json_encode($output));
    
                } else {
                    echo "0 Results";
                }
    
            } else {
                $sql = "SELECT * FROM Train_Norm_Claims WHERE Train_Norm_Claims.claim_norm_id NOT IN (SELECT VV_Map.claim_id FROM VV_Map WHERE user_id=?)";
    
                $stmt= $conn->prepare($sql);
    
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if(mysqli_num_rows($result) > 0) {
                    $row = $result->fetch_assoc();
                    
                    // TODO: may need to update
                    $user_id_qa = 1;

                    $qa_latest = 1;
                    $sql_qa = "SELECT * FROM Train_Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
                    $stmt =  $conn->prepare($sql_qa);
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
    
                        if (!is_null($correction_claim)){
                            $claim_text = $correction_claim;
                        } else {
                            $claim_text = $row['cleaned_claim'];
                        }
    
                        $output = (["claim_text" => $claim_text, "claim_speaker" => $row['speaker'], "claim_date" => $row['check_date'], "claim_source" => $row['source'],
                        "claim_hyperlink" => $row['hyperlink'], "questions" => $questions, "country_code" => $row['claim_loc']]);
                        echo(json_encode($output));
    
                    } else {
                        echo "0 Results";
                    }
                    update_table($conn, "UPDATE Annotators SET train_current_valid_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
                }
            }
        }
        $conn->close();
    
    } else if ($req_type == "submit-data") {
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT * FROM Train_Norm_Claims WHERE (claim_norm_id = (SELECT train_current_valid_task FROM Annotators WHERE user_id=?))";    

        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $phase_3_label = $_POST["annotation"]["label"];
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
        
        // TODO: udpate
        $user_id_qa = 1;

        $qa_latest = 1;
        $sql_qa = "SELECT * FROM Train_Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";
        $stmt = $conn->prepare($sql_qa);
        $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $user_id_qa);
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
                    
                    update_table($conn, "INSERT INTO Qaproblem (qa_id, claim_norm_id, user_id_qa, question_problems, answer_problems, 
                    answer_problems_second, answer_problems_third, date_made) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 'iiisssss', 
                    $row_qa['qa_id'], $row['claim_norm_id'], $user_id, $question_problems, $answer_problems, $answer_problems_second, $answer_problems_third, $date);
                }
            }else {
                echo "0 Results";
            }

            $valid_latest = 1;
            update_table($conn, "INSERT INTO VV_Map (user_id, claim_id, date_made, phase_3_label, justification, unreadable)
            VALUES (?, ?, ?, ?, ?, ?)", 'iisssi', $user_id, $row['claim_norm_id'], $date, $phase_3_label, $justification, $unreadable);

            update_table($conn, "UPDATE Annotators SET train_current_valid_task=0, train_finished_valid_annotations=train_finished_valid_annotations+1 WHERE user_id=?",'i', $user_id);
    
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

                $sql_problem = "SELECT * FROM Qaproblem WHERE qa_id=? AND user_id_qa=?";
                $stmt = $conn->prepare($sql_problem);
                $stmt->bind_param("ii", $row_qa['qa_id'], $user_id);
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
    
            $output = (["claim_norm_id" => $row['claim_norm_id'], "claim_text" => $claim_text, "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "claim_date" => $row['check_date'], "hyperlink" => $row['hyperlink'],  "questions" => $questions, "annotation" => $annotation, "country_code" => $row['claim_loc']]);
            echo(json_encode($output));
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

        $phase_3_label = $_POST["annotation"]["label"];
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
        
        $user_id_qa=1;

        $qa_latest=1;
        $sql_qa = "SELECT * FROM Train_Qapair WHERE claim_norm_id=? AND user_id_qa=? AND qa_latest=?";
        $stmt = $conn->prepare($sql_qa);
        $stmt->bind_param("iii", $claim_norm_id, $user_id_qa, $qa_latest);
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
                    
                    update_table($conn, "UPDATE Qaproblem SET question_problems=?, answer_problems=?, answer_problems_second=?,
                    answer_problems_third=? WHERE claim_norm_id=? AND user_id_qa=?",'ssssii', $question_problems, $answer_problems, 
                    $answer_problems_second, $answer_problems_third, $claim_norm_id, $user_id); 
                    
                }
            }else {
                echo "0 Results";
            }
    
            update_table($conn, "UPDATE VV_Map SET date_modified=?, phase_3_label=?, justification=?, unreadable=?
            WHERE claim_id=?",'sssii', $date, $phase_3_label, $justification, $unreadable, $claim_norm_id);

            $conn->commit();
            echo "Resubmit Successfully!";
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    }

} else {
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
    
    } else if ($req_type == "submit-data") {
        $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT * FROM Assigned_Valids WHERE (claim_norm_id = (SELECT current_valid_task FROM Annotators WHERE user_id=?))";
        
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $phase_3_label = $_POST["annotation"]["label"];
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
    
        $qa_latest = 1;
        $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=? ";
        $stmt = $conn->prepare($sql_qa);
        $stmt->bind_param("iii", $qa_latest, $row['claim_qa_id'], $row['user_id_qa']);
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
    
            $valid_latest = 1;
            update_table($conn, "UPDATE Assigned_Valids SET valid_annotators_num=valid_annotators_num+1, phase_3_label=?, justification=?, date_made_valid=?, 
            date_start_valid=?, unreadable=?, valid_latest=?
            WHERE claim_norm_id=?",'ssssiii', $phase_3_label, $justification, $date, $row['date_start_valid'], $unreadable, $valid_latest, $row['claim_norm_id']);
    
            $to_time = strtotime($date);
            $from_time = strtotime($row['date_start_valid']);
            $minutes = round(abs($to_time - $from_time) / 60,2);
            echo("The annotation time is: $minutes minutes.");
    
            $p3_speed_trap = 0;
            if ($minutes < 0.4) {
                $p3_speed_trap = 1;
            }
    
            update_table($conn, "UPDATE Annotators SET current_valid_task=0, finished_valid_annotations=finished_valid_annotations+1, p3_time_sum=p3_time_sum+?, p3_speed_trap=p3_speed_trap+? WHERE user_id=?",'ddi', $minutes, $p3_speed_trap, $user_id);
    
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
    
        $valid_annotated_num = 0;
        $valid_latest = 1;
        $sql = "SELECT claim_norm_id, claim_qa_id, user_id_qa, cleaned_claim, correction_claim, speaker, source, claim_loc, check_date, hyperlink, phase_3_label, justification, unreadable
         FROM Assigned_Valids WHERE valid_latest=? AND user_id_valid=? AND valid_annotators_num!=?  ORDER BY date_start_valid DESC LIMIT 1 OFFSET ?";
    
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("iiii", $valid_latest, $user_id, $valid_annotated_num, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $qa_latest = 1;
        $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";;
        $stmt = $conn->prepare($sql_qa);
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
            $annotation = array();
            $annotation['justification'] = $row['justification'];
            $annotation['label'] = $row['phase_3_label'];
            $annotation['unreadable'] = $row['unreadable'];
    
            if (!is_null($row["correction_claim"])){
                $claim_text = $row['correction_claim'];
            } else {
                $claim_text = $row['cleaned_claim'];
            }
    
            $output = (["claim_norm_id" => $row['claim_norm_id'], "claim_text" => $claim_text, "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "claim_date" => $row['check_date'], "hyperlink" => $row['hyperlink'],  "questions" => $questions, "annotation" => $annotation, "country_code" => $row['claim_loc']]);
            echo(json_encode($output));
    
            update_table($conn, "UPDATE Assigned_Valids SET date_restart_valid=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
    
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
    
        $sql = "SELECT * FROM Assigned_Valids WHERE claim_norm_id=?";
        $stmt= $conn->prepare($sql);
        $stmt->bind_param("i", $claim_norm_id);
        $stmt->execute();
        $result_time = $stmt->get_result();
        $row = $result_time->fetch_assoc();
    
        $valid_latest = 0;
        $sql_update = "UPDATE Assigned_Valids SET valid_latest=? WHERE claim_norm_id=?";
        $stmt= $conn->prepare($sql_update);
        $stmt->bind_param("ii", $valid_latest, $claim_norm_id);
        $stmt->execute();
    
        $phase_3_label = $_POST["annotation"]["label"];
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
    
        echo "first here";
        echo $unreadable;
    
        $qa_latest=1;
        $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND user_id_qa=? AND qa_latest=?";
        $stmt = $conn->prepare($sql_qa);
        $stmt->bind_param("iii", $row['claim_qa_id'], $row['user_id_qa'], $qa_latest);
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
    
            $to_time = strtotime($date);
            $from_time = strtotime($row['date_restart_valid']);
            $minutes = round(abs($to_time - $from_time) / 60,2);
            echo("The annotation time is: $minutes minutes.");
    
            update_table($conn, "UPDATE Annotators SET p3_time_sum=p3_time_sum+? WHERE user_id=?",'di', $minutes, $user_id);
            
            $valid_latest = 0;
            update_table($conn, "UPDATE Assigned_Valids SET valid_latest=? WHERE claim_norm_id=?",'ii', $valid_latest, $claim_norm_id);
    
            $valid_latest = 1;
            $inserted = 0;
            $valid_annotators_num = $row['valid_annotators_num']+1;
    
            echo "here";
            echo $unreadable;
            
            update_table($conn, "INSERT INTO Assigned_Valids (claim_id, claim_qa_id, web_archive, user_id_norm, user_id_qa, user_id_valid, cleaned_claim, 
            correction_claim, speaker, hyperlink, transcription, media_source, check_date, claim_types, fact_checker_strategy, phase_1_label, phase_2_label, qa_annotators_num, 
            qa_skipped, valid_annotators_num, num_qapairs, claim_loc, latest, source, date_start_norm, date_load_norm, date_made_norm, date_restart_norm, 
            date_modified_norm, date_start_qa, date_load_qa, date_made_qa, date_restart_qa, date_modified_qa, inserted, 
            date_start_valid, date_made_valid, date_restart_valid, date_modified_valid,
            phase_3_label, justification, unreadable, valid_latest) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            'iisiiisssssssssssiiiisisssssssssssissssssii', 
            $row['claim_id'], $row['claim_qa_id'], $row['web_archive'], $row['user_id_norm'], $row['user_id_qa'], $row['user_id_valid'], $row['cleaned_claim'], $row['correction_claim'], 
            $row['speaker'], $row['hyperlink'], $row['transcription'], $row['media_source'], $row['check_date'], $row['claim_types'], $row['fact_checker_strategy'], 
            $row['phase_1_label'], $row['phase_2_label'], $row['qa_annotators_num'], $row['qa_skipped'], $valid_annotators_num, $row['num_qapairs'],  
            $row['claim_loc'], $row['latest'], $row['source'], $row['date_start_norm'], $row['date_load_norm'], $row['date_made_norm'], $row['date_restart_norm'], 
            $row['date_modified_norm'], $row['date_start_qa'], $row['date_load_qa'], $row['date_made_qa'], $row['date_restart_qa'], $row['date_modified_qa'], $inserted, 
            $row['date_start_valid'], $row['date_made_valid'], $row['date_restart_valid'], $date,
            $phase_3_label, $justification, $unreadable, $valid_latest);
    
            $conn->commit();
            echo "Resubmit Successfully!";
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    }
}




?>