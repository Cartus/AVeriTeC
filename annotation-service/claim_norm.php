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

    $sql = "SELECT user_id, annotation_phase, current_norm_task FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(mysqli_num_rows($result) > 0){
        $row = $result->fetch_assoc();
        if ($row['current_norm_task'] != 0) {
            $sql = "SELECT claim_id, web_archive FROM Claims WHERE claim_id = ?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_norm_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); 
            $output = (["web_archive" => $row['web_archive'], "claim_id" => $row['claim_id']]);
            echo(json_encode($output));
        } else {
            $sql = "SELECT claim_id, web_archive FROM Claims WHERE norm_annotators_num = 0 AND norm_taken_flag=0 AND norm_skipped=0 ORDER BY RAND() LIMIT 1";
            $result = $conn->query($sql);
            if(mysqli_num_rows($result) > 0) {
                $row = $result->fetch_assoc(); 
                $output = (["web_archive" => $row['web_archive'], "claim_id" => $row['claim_id']]);
                echo(json_encode($output));
    
                $conn->begin_transaction();
                try {
                    if(!is_null($row['claim_id'])){
                        update_table($conn, "UPDATE Claims SET norm_taken_flag=1, user_id_norm=? WHERE claim_id=?", 'ii', $user_id, $row['claim_id']);
                        update_table($conn, "UPDATE Annotators SET current_norm_task=? WHERE user_id=?", 'ii', $row['claim_id'], $user_id);
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
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT claim_id, web_archive FROM Claims WHERE (claim_id = (SELECT current_norm_task FROM Annotators WHERE user_id=?))";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $qa_annotators_num = 0;
    $qa_taken_flag = 0;
    $qa_skipped = 0;

    $valid_annotators_num = 0;
    $valid_taken_flag = 0;
    $valid_skipped = 0;

    $has_qapairs = 0;

    $conn->begin_transaction();
    try {
	foreach($_POST['entries'] as $item) {
            $cleaned_claim = $item['cleaned_claim'];
            
            if (array_key_exists('speaker', $item)){
                $speaker = $item['speaker'];
            }else{
                $speaker = NULL;
            }

            if (array_key_exists('hyperlink', $item)){
                $hyperlink = $item['hyperlink'];
            }else{
                $hyperlink = NULL;
            }

            if (array_key_exists('transcription', $item)){
                $transcription = $item['transcription'];
            }else{
                $transcription = NULL;
            }

            if (array_key_exists('media_source', $item)){
                $media_source = $item['media_source'];
            }else{
                $media_source = NULL;
            }

            if (array_key_exists('date', $item)){
                $check_date = substr($item['date'], 0, 10);
            }else{
                $check_date= NULL;
            }

            if (array_key_exists('location', $item)){
                $claim_loc = $item['location'];
            }else{
                $claim_loc= NULL;
            }

            $claim_types = $item['claim_types'];
            $fact_checker_strategy = $item['fact_checker_strategy'];
            $phase_1_label = $item['phase_1_label'];

            $claim_types = implode(" [SEP] ", $claim_types);
	    $fact_checker_strategy = implode(" [SEP] ", $fact_checker_strategy);

            update_table($conn, "INSERT INTO Norm_Claims (claim_id, web_archive, user_id_norm, cleaned_claim, speaker, hyperlink, transcription, media_source,
            check_date, claim_types, fact_checker_strategy, phase_1_label, qa_annotators_num, qa_taken_flag, qa_skipped, valid_annotators_num, valid_taken_flag, 
            has_qapairs, date_made_norm, claim_loc) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'isisssssssssiiiiiiss', $row['claim_id'], $row['web_archive'], $user_id, $cleaned_claim, $speaker, 
            $hyperlink, $transcription, $media_source, $check_date, $claim_types, $fact_checker_strategy, $phase_1_label, $qa_annotators_num, $qa_taken_flag, $qa_skipped,
            $valid_annotators_num, $valid_taken_flag, $has_qapairs, $date, $claim_loc);
        }
        
        update_table($conn, "UPDATE Claims SET norm_taken_flag=0, norm_annotators_num = norm_annotators_num+1, date_made_norm=? WHERE claim_id=?",'si', $date, $row['claim_id']);
        update_table($conn, "UPDATE Annotators SET current_norm_task=0, finished_norm_annotations=finished_norm_annotations+1  WHERE user_id=?",'i', $user_id);
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

    $sql = "SELECT claim_id, web_archive FROM Claims WHERE user_id_norm = ? ORDER BY date_made_norm DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); 

    $sql_norm = "SELECT * FROM Norm_Claims WHERE claim_id=? AND user_id_norm=?";;
    $stmt = $conn->prepare($sql_norm);
    $stmt->bind_param("ii", $row['claim_id'], $user_id);
    $stmt->execute();
    $result_norm = $stmt->get_result();

    $entries = array();
    $counter = 0;
    if ($result_norm->num_rows > 0) {
        while($row_norm = $result_norm->fetch_assoc()) {
            $count_string = "claim_entry_field_" . (string)$counter;
            $counter = $counter + 1;
            $norm_array = array();
            $norm_array['fact_checker_strategy'] = explode(" [SEP] ", $row_norm['fact_checker_strategy']);
            $norm_array['claim_types'] = explode(" [SEP] ", $row_norm['claim_types']);
            $norm_array['cleaned_claim'] = $row_norm['cleaned_claim'];
            $norm_array['phase_1_label'] = $row_norm['phase_1_label'];
            $norm_array['speaker'] = $row_norm['speaker'];
            $norm_array['hyperlink'] = $row_norm['hyperlink'];
            $norm_array['transcription'] = $row_norm['transcription'];
            $norm_array['media_source'] = $row_norm['media_source'];
            $norm_array['date'] = $row_norm['check_date'];
            $norm_array['location'] = $row_norm['claim_loc'];
            $entries[$count_string] = $norm_array;
        }
        $output = (["claim_id" => $row['claim_id'], "web_archive" => $row['web_archive'], "entries" => $entries]);
        echo(json_encode($output));
    } else {
        echo "0 Results";
    }
    $conn->close();
} else if ($req_type == "resubmit-data") {
    $claim_id = $_POST['claim_id'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql_del = "DELETE FROM Norm_Claims WHERE claim_id=? AND user_id_norm=?";
    $stmt= $conn->prepare($sql_del);
    $stmt->bind_param("ii", $claim_id, $user_id);
    $stmt->execute();

    $sql = "SELECT web_archive FROM Claims WHERE claim_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $claim_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); 

    $qa_annotators_num = 0;
    $qa_taken_flag = 0;
    $qa_skipped = 0;

    $valid_annotators_num = 0;
    $valid_taken_flag = 0;
    $valid_skipped = 0;

    $has_qapairs = 0;

    $conn->begin_transaction();
    try {
        foreach($_POST['entries'] as $item) {
            $cleaned_claim = $item['cleaned_claim'];
            
            if (array_key_exists('speaker', $item)){
                $speaker = $item['speaker'];
            }else{
                $speaker = NULL;
            }

            if (array_key_exists('hyperlink', $item)){
                $hyperlink = $item['hyperlink'];
            }else{
                $hyperlink = NULL;
            }

            if (array_key_exists('transcription', $item)){
                $transcription = $item['transcription'];
            }else{
                $transcription = NULL;
            }

            if (array_key_exists('media_source', $item)){
                $media_source = $item['media_source'];
            }else{
                $media_source = NULL;
            }

            if (array_key_exists('date', $item)){
                $check_date = substr($item['date'], 0, 10);
            }else{
                $check_date= NULL;
            }

            if (array_key_exists('location', $item)){
                $claim_loc = $item['location'];
            }else{
                $claim_loc= NULL;
            }

            $claim_types = $item['claim_types'];
            $fact_checker_strategy = $item['fact_checker_strategy'];
            $phase_1_label = $item['phase_1_label'];

            $claim_types = implode(" [SEP] ", $claim_types);
            $fact_checker_strategy = implode(" [SEP] ", $fact_checker_strategy);

            update_table($conn, "INSERT INTO Norm_Claims (claim_id, web_archive, user_id_norm, cleaned_claim, speaker, hyperlink, transcription, media_source,
            check_date, claim_types, fact_checker_strategy, phase_1_label, qa_annotators_num, qa_taken_flag, qa_skipped, valid_annotators_num, valid_taken_flag, 
            has_qapairs, date_made_norm, claim_loc) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'isisssssssssiiiiiiss', $claim_id, $row['web_archive'], $user_id, $cleaned_claim, $hyperlink,
            $speaker, $transcription, $media_source, $check_date, $claim_types, $fact_checker_strategy, $phase_1_label, $qa_annotators_num, $qa_taken_flag, $qa_skipped,
            $valid_annotators_num, $valid_taken_flag, $has_qapairs, $date, $claim_loc);
        }
        update_table($conn, "UPDATE Claims SET norm_annotators_num = norm_annotators_num+1, date_modified_norm=? WHERE claim_id=?",'si', $date, $claim_id);
        $conn->commit();
        echo "Resubmit Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
} else if ($req_type == "skip-data") {

    $claim_id = $_POST['claim_id'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->begin_transaction();
    try {
        update_table($conn, "UPDATE Claims SET norm_skipped=1, norm_skipped_by=? WHERE claim_id=?",'ii', $user_id, $claim_id);
        update_table($conn, "UPDATE Annotators SET current_norm_task=0 WHERE user_id=?",'i', $user_id);
        $conn->commit();
        echo "Skip Successfully!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
}



?>
