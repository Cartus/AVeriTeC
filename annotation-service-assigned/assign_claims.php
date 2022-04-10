<?php

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt = $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$date = date("Y-m-d H:i:s");

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['user_id'];
$per_user = $_POST['assignments_per_user'];
$user_ids = $_POST['assignment_user_ids'];
$phase = $_POST['req_type'];

$total_users = count($user_ids);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
}

if ($phase == 1) {
    foreach($user_ids as $item){
        $counter = 0;
        while($counter < $per_user) {
            $sql = "SELECT * FROM Claims WHERE inserted=0 ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
    
            update_table($conn, "UPDATE Claims SET inserted=1 WHERE claim_id=?", "i", $row['claim_id']);
    
            update_table($conn, "INSERT INTO Assigned_Claims (claim_text, web_archive, claim_date, user_id_norm, norm_annotators_num, norm_skipped)
            VALUES(?, ?, ?, ?, ?, ?)", "sssiii", $row['claim_text'], $row['web_archive'], $row['claim_date'], $item, 0, 0);

            $counter++;
        } 
        update_table($conn, "UPDATE Annotators SET p1_assigned=p1_assigned+? WHERE user_id=?", "ii", $counter, $item);
        echo "Inserted successfully";
    }
} elseif ($phase == 2) {
    foreach($user_ids as $item){
        $counter = 0;
        while($counter < $per_user) {
            $sql = "SELECT * FROM Norm_Claims WHERE inserted=0 AND latest=1 AND nonfactual=0 AND user_id_norm!=? ORDER BY RAND() LIMIT 1";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $item);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
    
            echo $row['cleaned_claim'];

            $inserted_valid = 0;

            update_table($conn, "UPDATE Norm_Claims SET inserted=1 WHERE claim_norm_id=?", "i", $row['claim_norm_id']);

            $qa_annotators_num = 0;
            $qa_skipped = 0;
            $has_qapairs = 0;

            update_table($conn, "INSERT INTO Assigned_Norms (claim_id, web_archive, user_id_norm, user_id_qa, cleaned_claim, speaker, hyperlink, transcription, media_source,
            check_date, claim_types, fact_checker_strategy, phase_1_label, qa_annotators_num, qa_skipped, 
            has_qapairs, claim_loc, latest, source, nonfactual, date_start_norm, date_load_norm, date_made_norm, date_restart_norm, date_modified_norm, inserted_valid) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'isiisssssssssiiisisisssssi', 
            $row['claim_id'], $row['web_archive'], $row['user_id_norm'], $item, $row['cleaned_claim'], $row['speaker'], $row['hyperlink'], $row['transcription'], 
            $row['media_source'], $row['check_date'], $row['claim_types'], $row['fact_checker_strategy'], $row['phase_1_label'], $qa_annotators_num,  
            $qa_skipped, $has_qapairs, $row['claim_loc'], $row['latest'], $row['source'], $row['nonfactual'], $row['date_start_norm'], $row['date_load_norm'], 
            $row['date_made_norm'], $row['date_restart_norm'], $row['date_modified_norm'], $inserted_valid);

            $counter++;
        } 

    // condition: if the claim is really assignd to the annotator. For other phases also.
    update_table($conn, "UPDATE Annotators SET p2_assigned=p2_assigned+? WHERE user_id=?", "ii", $counter, $item);
    echo "Inserted successfully";
    }
} elseif ($phase == 3) {
    foreach($user_ids as $item){
        $counter = 0;
        while($counter < $per_user) {
            $sql = "SELECT * FROM Assigned_Norms WHERE inserted_valid=0 AND has_qapairs=1 AND latest=1 AND user_id_norm!=? AND user_id_qa!=? ORDER BY RAND() LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item, $item);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            $inserted_valid = 0;
            $valid_annotators_num = 0;

            update_table($conn, "UPDATE Assigned_Norms SET inserted_valid=1 WHERE claim_norm_id=?", "i", $row['claim_norm_id']);

            update_table($conn, "INSERT INTO Assigned_Valids (claim_id, claim_qa_id, web_archive, user_id_norm, user_id_qa, user_id_valid, cleaned_claim, correction_claim, speaker, hyperlink, transcription, media_source,
            check_date, claim_types, fact_checker_strategy, phase_1_label, phase_2_label, qa_annotators_num, qa_skipped, valid_annotators_num, 
            has_qapairs, num_qapairs, claim_loc, latest, source, date_start_norm, date_load_norm, date_made_norm, date_restart_norm, date_modified_norm, 
            date_start_qa, date_load_qa, date_made_qa, date_restart_qa, date_modified_qa, inserted_valid) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            'iisiiisssssssssssiiiiisisssssssssssi', 
            $row['claim_id'], $row['claim_norm_id'], $row['web_archive'], $row['user_id_norm'], $row['user_id_qa'], $item, $row['cleaned_claim'], $row['correction_claim'], 
            $row['speaker'], $row['hyperlink'], $row['transcription'], $row['media_source'], $row['check_date'], $row['claim_types'], $row['fact_checker_strategy'], 
            $row['phase_1_label'], $row['phase_2_label'], $row['qa_annotators_num'], $row['qa_skipped'], $valid_annotators_num, $row['num_qapairs'], $row['has_qapairs'], 
            $row['claim_loc'], $row['latest'], $row['source'], $row['date_start_norm'], $row['date_load_norm'], $row['date_made_norm'], $row['date_restart_norm'], 
            $row['date_modified_norm'], $row['date_start_qa'], $row['date_load_qa'], $row['date_made_qa'], $row['date_restart_qa'], $row['date_modified_qa'], $inserted_valid);

            $counter++;
        } 
    update_table($conn, "UPDATE Annotators SET p3_assigned=p3_assigned+? WHERE user_id=?", "ii", $counter, $item);
    echo "Inserted successfully";
    }
} elseif ($phase == 4) {
    echo 4;
} elseif ($phase == 5) {
    echo 5;
}




$conn->close();
?>