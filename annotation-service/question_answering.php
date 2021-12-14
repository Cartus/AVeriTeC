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

    $sql = "SELECT user_id, annotation_phase, current_qa_task, finished_qa_annotations FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_qa_task'] != 0) {
            $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, source, check_date, claim_loc FROM Norm_Claims WHERE claim_norm_id = ?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_qa_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id']]);
            echo(json_encode($output));
        } else {
            $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, source, check_date, claim_loc, user_id_norm FROM Norm_Claims
            WHERE qa_annotators_num = 0 AND qa_taken_flag=0 AND qa_skipped=0 AND latest=1 AND user_id_norm = ? ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);

            if ($user_id == 3) {
                $user_id_norm = 1;
            } else {
                $user_id_norm = $user_id + 1;
            }

            $stmt->bind_param("i", $user_id_norm);
            $stmt->execute();
            $result = $stmt->get_result();

            $conn->begin_transaction();
            try {
                if(mysqli_num_rows($result) > 0) {
                    $row = $result->fetch_assoc();
                    $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                    "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id'], "user_id_norm" => $row['user_id_norm']]);
                    echo(json_encode($output));
                    update_table($conn, "UPDATE Norm_Claims SET qa_taken_flag=1, user_id_qa=? WHERE claim_norm_id=?", 'ii', $user_id, $row['claim_norm_id']);
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

    $sql = "SELECT claim_norm_id FROM Norm_Claims WHERE (claim_norm_id = (SELECT current_qa_task FROM Annotators WHERE user_id=?))";
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

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            // print_r($item);
            $question = $item['question'];
            $answers = $item['answers'];

            $answer = $answers[0]['answer'];

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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssisssssssssss', $row['claim_norm_id'], $user_id, $question, $answer,
            $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
            $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third);
        }

        update_table($conn, "UPDATE Norm_Claims SET qa_taken_flag=0, has_qapairs=1, qa_annotators_num = qa_annotators_num+1, phase_2_label=?, num_qapairs=?, date_made_qa=?, correction_claim=?
        WHERE claim_norm_id=?",'sissi', $phase_2_label, $num_qapairs, $date, $correction_claim, $row['claim_norm_id']);
        update_table($conn, "UPDATE Annotators SET current_qa_task=0, finished_qa_annotations=finished_qa_annotations+1  WHERE user_id=?",'i', $user_id);
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

    $sql = "SELECT qa_skipped, claim_norm_id, web_archive, cleaned_claim, source, speaker, check_date, hyperlink, phase_2_label, claim_loc, correction_claim
     FROM Norm_Claims WHERE user_id_qa = ? ORDER BY date_made_qa DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

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

        $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $row['phase_2_label'], "country_code" => $row['claim_loc'],
            "claim_correction" => $row['correction_claim'], "should_correct" => $should_correct]);
        echo(json_encode($output));
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

            $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $row['phase_2_label'], "country_code" => $row['claim_loc'],
            "claim_correction" => $row['correction_claim'], "should_correct" => $should_correct]);
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
    $sql_update = "UPDATE Qapair SET qa_latest=? WHERE claim_norm_id=? AND user_id_qa=?";
    $stmt= $conn->prepare($sql_update);
    $stmt->bind_param("iii", $qa_latest, $claim_norm_id, $user_id);
    $stmt->execute();

    $sql = "SELECT web_archive FROM Norm_Claims WHERE claim_norm_id = ?";
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

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            $question = $item['question'];
            $answers = $item['answers'];

            $answer = $answers[0]['answer'];

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
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssisssssssssss', $claim_norm_id, $user_id, $question, $answer,
            $source_url, $answer_type, $source_medium, $qa_latest, $bool_explanation, $answer_second, $source_url_second, $answer_type_second, $source_medium_second,
            $bool_explanation_second, $answer_third, $source_url_third, $answer_type_third, $source_medium_third, $bool_explanation_third);
        }

        update_table($conn, "UPDATE Norm_Claims SET qa_skipped=0, qa_annotators_num = qa_annotators_num+1, phase_2_label=?, num_qapairs=?, date_modified_qa=?, correction_claim=?
        WHERE claim_norm_id=?",'sissi', $phase_2_label, $num_qapairs, $date, $correction_claim, $claim_norm_id);
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

    $conn->begin_transaction();
    try {
	update_table($conn, "UPDATE Norm_Claims SET qa_skipped=1, qa_skipped_by=?, date_made_qa=? WHERE claim_norm_id=?",'isi', $user_id, $date, $claim_norm_id);    
        update_table($conn, "UPDATE Annotators SET current_qa_task=0, skipped_qa_data=skipped_qa_data+1, finished_qa_annotations=finished_qa_annotations+1 WHERE user_id=?",'i', $user_id);
        $conn->commit();
        echo "Skip Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
}



?>
