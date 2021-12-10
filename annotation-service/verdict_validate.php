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

// if (empty($user_id) && empty($req_type)) die();

$user_id = $_POST['user_id'];
$req_type = $_POST['req_type'];

if ($req_type == "next-data"){

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT user_id, annotation_phase, current_valid_task FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_valid_task'] != 0) {
            $sql = "SELECT claim_norm_id, user_id_qa, web_archive, cleaned_claim, speaker, source, check_date, hyperlink, claim_loc FROM Norm_Claims WHERE claim_norm_id = ?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_valid_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND user_id_qa=?";;
            $stmt = $conn->prepare($sql_qa);
            $stmt->bind_param("ii", $row['claim_norm_id'], $row['user_id_qa']);
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

                    $question_array['answers'] = $answers;
                    $questions[$count_string] = $question_array;
                }
            } else {
                echo "0 Results";
            }

            $output = (["web_archive" => $row['web_archive'], "claim_text" => $row['cleaned_claim'], "claim_speaker" => $row['speaker'], "claim_source" => $row['source'],
            "claim_date" => $row['check_date'], "claim_hyperlink" => $row['hyperlink'], "questions" => $questions, "country_code" => $row['claim_loc']]);
            echo(json_encode($output));
        } else {
            $sql = "SELECT claim_norm_id, user_id_qa, web_archive, cleaned_claim, speaker, check_date, claim_types, fact_checker_strategy, hyperlink FROM Norm_Claims
            WHERE valid_annotators_num = 0 AND valid_taken_flag=0 AND has_qapairs=1 AND latest=1 AND user_id_norm=? AND user_id_qa=? ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);

            if ($user_id == 3) {
                $user_id2 = 1;
                $user_id1 = 2;
            } else if ($user_id == 2) {
                $user_id2 = 3;
                $user_id1 = 1;
            } else if ($user_id == 1) {
                $user_id2 = 2;
                $user_id1 = 3;
            }

            $stmt->bind_param("ii", $user_id1, $user_id2);
            $stmt->execute();
            $result = $stmt->get_result();

            if(mysqli_num_rows($result) > 0) {
                $row = $result->fetch_assoc();

                $qa_latest = 1;
                $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";;
                $stmt =  $conn->prepare($sql_qa);
                $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $row['user_id_qa']);
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
                        $question_array['answer'] = $row_qa['answer'];
                        $question_array['url'] = $row_qa['source_url'];
                        $questions[$count_string] = $question_array;
                    }
                } else {
                    echo "0 Results";
                }
                $output = (["web_archive" => $row['web_archive'], "claim_text" => $row['cleaned_claim'], "claim_speaker" => $row['speaker'], "claim_date" => $row['check_date'],
                "claim_hyperlink" => $row['hyperlink'], "questions" => $questions]);
                echo(json_encode($output));

                $conn->begin_transaction();
                try {
                    if(!is_null($row['claim_norm_id'])){
                        update_table($conn, "UPDATE Norm_Claims SET valid_taken_flag=1, user_id_valid=? WHERE claim_norm_id=?", 'ii', $user_id, $row['claim_norm_id']);
                        update_table($conn, "UPDATE Annotators SET current_valid_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
                    }
                    $conn->commit();
                }catch (mysqli_sql_exception $exception) {
                    $conn->rollback();
                    throw $exception;
                }
            }
        }
    }
    $conn->close();

} else if ($req_type == "submit-data") {
    print_r($_POST["questions"]);

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT claim_norm_id FROM Norm_Claims WHERE (claim_norm_id = (SELECT current_valid_task FROM Annotators WHERE user_id=?))";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $phase_3_label = $_POST["annotation"]["label"];
    $justification = $_POST["annotation"]["justification"];

    if (array_key_exists("unreadable", $_POST["annotation"])) {
        if ($_POST["annotation"]["unreadable"] == "1") {
            $bias = 1;
        } else {
            $bias = 0;
        }
    } else {
        $bias = 0;
    }

    $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=?";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("i", $row['claim_norm_id']);
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
        update_table($conn, "UPDATE Norm_Claims SET valid_taken_flag=0, valid_annotators_num = valid_annotators_num+1, phase_3_label=?, justification=?, date_made_valid=?, bias=?
        WHERE claim_norm_id=?",'sssii', $phase_3_label, $justification, $date, $bias, $row['claim_norm_id']);
        update_table($conn, "UPDATE Annotators SET current_valid_task=0, finished_valid_annotations=finished_valid_annotations+1 WHERE user_id=?",'i', $user_id);
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

    $sql = "SELECT claim_norm_id, user_id_qa, web_archive, cleaned_claim, speaker, source, claim_loc, check_date, hyperlink, phase_3_label, justification
     FROM Norm_Claims WHERE user_id_valid = ? ORDER BY date_made_valid DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $qa_latest = 1;
    $sql_qa = "SELECT * FROM Qapair WHERE qa_latest=? AND claim_norm_id=? AND user_id_qa=?";;
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("iii", $qa_latest, $row['claim_norm_id'], $row['user_id_qa']);
    $stmt->execute();
    $result_qa = $stmt->get_result();

    $questions = array();
    $counter = 0;
    if ($result_qa->num_rows > 0) {
        while($row_qa = $result_qa->fetch_assoc()) {
            $counter = $counter + 1;
            $count_string = "question_" . (string)$counter;
            $question_array = array();

            $question_array['question_problems'] = explode(" [SEP] ", $row_qa['question_problems']);

            $answers = array();
            $answers[0]['answer'] = $row_qa['answer'];
            $answers[0]['source_url'] = $row_qa['source_url'];
            $answers[0]['answer_type'] = $row_qa['answer_type'];
            $answers[0]['source_medium'] = $row_qa['source_medium'];

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

            if (!is_null($row_qa['answer_problems_third'])){
                $answers[2]['answer_problems'] = explode(" [SEP] ", $row_qa['answer_problems_third']);
            }

            $question_array['answers'] = $answers;

            $questions[$count_string] = $question_array;
        }
        $annotation = array();
        $annotation['justification'] = $row['justification'];
        $annotation['label'] = $row['phase_3_label'];
        // $annotation['bias'] = $row['bias'];
        $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "claim_text" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
        "claim_date" => $row['check_date'], "hyperlink" => $row['hyperlink'],  "questions" => $questions, "annotation" => $annotation, "country_code" => $row['claim_loc']]);
        echo(json_encode($output));
    } else {
        echo "0 Results";
    }
    $conn->close();
} else if ($req_type == "resubmit-data") {

    // print_r($_POST["questions"]);

    $claim_norm_id = $_POST['claim_norm_id'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $phase_3_label = $_POST["annotation"]["label"];
    $justification = $_POST["annotation"]["justification"];
    if (array_key_exists("bias", $_POST["annotation"])) {
        if ($_POST["annotation"]["bias"] == "1") {
            $bias = 1;
        } else {
            $bias = 0;
        }
    } else {
        $bias = 0;
    }

    $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=?";
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("i", $claim_norm_id);
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

                // echo $answer_problems;
                // echo $answer_problems_second;
                // echo $answer_problems_third;

                update_table($conn, "UPDATE Qapair SET question_problems=?, answer_problems=?, answer_problems_second=?,
                answer_problems_third=? WHERE qa_id=?",'ssssi', $question_problems, $answer_problems, $answer_problems_second, $answer_problems_third, $row_qa['qa_id']);
            }
        }else {
            echo "0 Results";
        }

        update_table($conn, "UPDATE Norm_Claims SET valid_annotators_num = valid_annotators_num+1, phase_3_label=?, justification=?, date_modified_valid=?, bias=?
        WHERE claim_norm_id=?",'sssii', $phase_3_label, $justification, $date, $bias, $claim_norm_id);
        $conn->commit();
        echo "Resubmit Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
}


?>
