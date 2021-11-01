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

    $sql = "SELECT user_id, annotation_phase, current_qa_task FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_qa_task'] != 0) {
            $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, check_date, claim_loc FROM Norm_Claims WHERE claim_norm_id = ?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_qa_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); 
            $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], 
            "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id']]);
            echo(json_encode($output));
        } else {
            $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, check_date, claim_loc FROM Norm_Claims 
            WHERE qa_annotators_num = 0 AND qa_taken_flag=0 AND qa_skipped=0 AND user_id_norm != ? ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $conn->begin_transaction();
            try {
                if(mysqli_num_rows($result) > 0) {
                    $row = $result->fetch_assoc();
                    $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], 
                    "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id']]);
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

    $phase_2_label = $_POST["qa_pair_header"]["label"];
    $num_qapairs = $_POST["added_entries"];

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            $question = $item['question'];
            $answer = $item['answer'];

            if (array_key_exists('source_url', $item)){
                $source_url = $item['source_url'];
            }else{
                $source_url = NULL;
            }

            if (array_key_exists('answer_type', $item)){
                $answer_type = $item['answer_type'];
            }else{
                $answer_type = NULL;
            }

            if (array_key_exists('source_medium', $item)){
                $source_medium = $item['source_medium'];
            }else{
                $source_medium = NULL;
            }

            update_table($conn, "INSERT INTO Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium) 
            VALUES (?, ?, ?, ?, ?, ?, ?)", 'iisssss', $row['claim_norm_id'], $user_id, $question, $answer,
            $source_url, $answer_type, $source_medium);
        }
        
        update_table($conn, "UPDATE Norm_Claims SET qa_taken_flag=0, has_qapairs=1, qa_annotators_num = qa_annotators_num+1, phase_2_label=?, num_qapairs=?, date_made_qa=?
        WHERE claim_norm_id=?",'sisi', $phase_2_label, $num_qapairs, $date, $row['claim_norm_id']);
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

    $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, check_date, hyperlink, phase_2_label, claim_loc
     FROM Norm_Claims WHERE user_id_qa = ? ORDER BY date_made_qa DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); 

    $sql_qa = "SELECT * FROM Qapair WHERE claim_norm_id=? AND user_id_qa=?";;
    $stmt = $conn->prepare($sql_qa);
    $stmt->bind_param("ii", $row['claim_norm_id'], $user_id);
    $stmt->execute();
    $result_qa = $stmt->get_result();

    $questions = array();
    $counter = 0;

    $entries = array();
    // {"entries":{"qa_pair_entry_field_0":{"answer_type":"Extractive","source_medium":"Image/graphic","source_url":"u","question":"question","answer":"answer"}},"added_entries":1,"valid":true,"qa_pair_header":{"label":"Supported"}}
    if ($result_qa->num_rows > 0) {
        while($row_qa = $result_qa->fetch_assoc()) {
            $count_string = "qa_pair_entry_field_" . (string)$counter;
            $counter = $counter + 1;
            $field_array = array();
            $field_array['answer_type'] = $row_qa['answer_type'];
            $field_array['source_medium'] = $row_qa['source_medium'];
            $field_array['source_url'] = $row_qa['source_url'];
            $field_array['question'] = $row_qa['question'];
            $field_array['answer'] = $row_qa['answer'];
            $entries[$count_string] = $field_array;
        }

        $output = (["claim_norm_id" => $row['claim_norm_id'], "web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], 
        "check_date" => $row['check_date'], "hyperlink" => $row['hyperlink'], "entries" => $entries, "label" => $row['phase_2_label'], "country_code" => $row['claim_loc']]);
        echo(json_encode($output));
    } else {
        echo "0 Results";
    }
    $conn->close();
} else if ($req_type == "resubmit-data") {
    $claim_norm_id = $_POST['claim_norm_id'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql_del = "DELETE FROM Qapair WHERE claim_norm_id=? AND user_id_qa=?";
    $stmt= $conn->prepare($sql_del);
    $stmt->bind_param("ii", $claim_norm_id, $user_id);
    $stmt->execute();

    $sql = "SELECT web_archive FROM Norm_Claims WHERE claim_norm_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $claim_norm_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); 

    $phase_2_label = $_POST["qa_pair_header"]["label"];
    $num_qapairs = $_POST["added_entries"];

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            $question = $item['question'];
            $answer = $item['answer'];

            if (array_key_exists('source_url', $item)){
                $source_url = $item['source_url'];
            }else{
                $source_url = NULL;
            }

            if (array_key_exists('answer_type', $item)){
                $answer_type = $item['answer_type'];
            }else{
                $answer_type = NULL;
            }

            if (array_key_exists('source_medium', $item)){
                $source_medium = $item['source_medium'];
            }else{
                $source_medium = NULL;
            }

            update_table($conn, "INSERT INTO Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium) 
            VALUES (?, ?, ?, ?, ?, ?, ?)", 'iisssss', $claim_norm_id, $user_id, $question, $answer,
            $source_url, $answer_type, $source_medium);
        }
        
        update_table($conn, "UPDATE Norm_Claims SET qa_annotators_num = qa_annotators_num+1, phase_2_label=?, num_qapairs=?, date_modified_qa=?
        WHERE claim_norm_id=?",'sisi', $phase_2_label, $num_qapairs, $date, $claim_norm_id);
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
        update_table($conn, "UPDATE Norm_Claims SET qa_skipped=1, qa_skipped_by=? WHERE claim_norm_id=?",'ii', $user_id, $claim_norm_id);
        update_table($conn, "UPDATE Annotators SET current_qa_task=0 WHERE user_id=?",'i', $user_id);
        $conn->commit();
        echo "Skip Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
}




?>
