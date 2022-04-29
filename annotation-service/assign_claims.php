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
        if ($per_user > 0) {
            while($counter < $per_user) {
                $sql = "SELECT * FROM Claims WHERE inserted=0 ORDER BY RAND() LIMIT 1";
                $stmt= $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if (mysqli_num_rows($result)==0) { 
                    break; 
                }

                $row = $result->fetch_assoc();
        
                update_table($conn, "UPDATE Claims SET inserted=1 WHERE claim_id=?", "i", $row['claim_id']);
        
                update_table($conn, "INSERT INTO Assigned_Claims (raw_id, claim_text, web_archive, claim_date, user_id_norm, norm_annotators_num, norm_skipped)
                VALUES (?, ?, ?, ?, ?, ?, ?)", "isssiii", $row['claim_id'], $row['claim_text'], $row['web_archive'], $row['claim_date'], $item, 0, 0);
    
                $counter++;
            } 
            update_table($conn, "UPDATE Annotators SET p1_assigned=p1_assigned+? WHERE user_id=?", "ii", $counter, $item);
            echo "Inserted successfully";
        } else {
            while($counter < -$per_user) {
                $sql = "SELECT * FROM Assigned_Claims WHERE norm_annotators_num=0 ORDER BY RAND() LIMIT 1";
                $stmt= $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if (mysqli_num_rows($result)==0) { 
                    break; 
                }

                $row = $result->fetch_assoc();

                update_table($conn, "UPDATE Claims SET inserted=0 WHERE claim_id=?", "i", $row['raw_id']);

                update_table($conn, "DELETE FROM Assigned_Claims WHERE claim_id=?", "i", $row['claim_id']);
        
                $counter++;
            }
            update_table($conn, "UPDATE Annotators SET current_norm_task=NULL, p1_assigned=p1_assigned-? WHERE user_id=?", "ii", $counter, $item);
            echo "Removed successfully";
        }
    }
} elseif ($phase == 2) {
    foreach($user_ids as $item){
        $counter = 0;
        if ($per_user > 0) {
            while($counter < $per_user) {
                $sql = "SELECT * FROM Norm_Claims WHERE inserted=0 AND latest=1 AND nonfactual=0 AND user_id_norm!=? ORDER BY RAND() LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $item);
                $stmt->execute();
                $result = $stmt->get_result();

                if (mysqli_num_rows($result)==0) { 
                    break; 
                }

                $row = $result->fetch_assoc();
        
                $inserted = 0;
    
                update_table($conn, "UPDATE Norm_Claims SET inserted=1 WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
    
                $qa_annotators_num = 0;
                $qa_skipped = 0;
                $num_qapairs = 0;
    
                update_table($conn, "INSERT INTO Assigned_Norms (claim_id, web_archive, user_id_norm, user_id_qa, cleaned_claim, speaker, hyperlink, transcription, media_source,
                check_date, claim_types, fact_checker_strategy, phase_1_label, qa_annotators_num, qa_skipped, 
                num_qapairs, claim_loc, latest, source, nonfactual, date_start_norm, date_load_norm, date_made_norm, date_restart_norm, date_modified_norm, inserted) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'isiisssssssssiiisisisssssi', 
                $row['claim_norm_id'], $row['web_archive'], $row['user_id_norm'], $item, $row['cleaned_claim'], $row['speaker'], $row['hyperlink'], $row['transcription'], 
                $row['media_source'], $row['check_date'], $row['claim_types'], $row['fact_checker_strategy'], $row['phase_1_label'], $qa_annotators_num,  
                $qa_skipped, $num_qapairs, $row['claim_loc'], $row['latest'], $row['source'], $row['nonfactual'], $row['date_start_norm'], $row['date_load_norm'], 
                $row['date_made_norm'], $row['date_restart_norm'], $row['date_modified_norm'], $inserted);
    
                $counter++;
            } 
            update_table($conn, "UPDATE Annotators SET p2_assigned=p2_assigned+? WHERE user_id=?", "ii", $counter, $item);
            echo "Inserted successfully";
        } else {
            while($counter < -$per_user) {
                $sql = "SELECT * FROM Assigned_Norms WHERE qa_annotators_num=0 ORDER BY RAND() LIMIT 1";
                $stmt= $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if (mysqli_num_rows($result)==0) { 
                    break; 
                }

                $row = $result->fetch_assoc();

                update_table($conn, "UPDATE Norm_Claims SET inserted=0 WHERE claim_norm_id=?", "i", $row['claim_id']);

                update_table($conn, "DELETE FROM Assigned_Norms WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
        
                $counter++;
            }
            update_table($conn, "UPDATE Annotators SET current_qa_task=NULL, p2_assigned=p2_assigned-? WHERE user_id=?", "ii", $counter, $item);
            echo "Removed successfully";
        }    
    }
} elseif ($phase == 3) {
    foreach($user_ids as $item){
        $counter = 0;
        if ($per_user > 0) {
            while($counter < $per_user) {
                $num_qapairs = 0;
                $sql = "SELECT * FROM Assigned_Norms WHERE inserted=0 AND latest=1 AND num_qapairs!=? AND user_id_norm!=? AND user_id_qa!=? ORDER BY RAND() LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $num_qapairs, $item, $item);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
    
                $inserted = 0;
                $valid_annotators_num = 0;
    
                update_table($conn, "UPDATE Assigned_Norms SET inserted=1 WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
    
                update_table($conn, "INSERT INTO Assigned_Valids (claim_id, claim_qa_id, web_archive, user_id_norm, user_id_qa, user_id_valid, cleaned_claim, correction_claim, speaker, hyperlink, transcription, media_source,
                check_date, claim_types, fact_checker_strategy, phase_1_label, phase_2_label, qa_annotators_num, qa_skipped, valid_annotators_num, 
                num_qapairs, claim_loc, latest, source, date_start_norm, date_load_norm, date_made_norm, date_restart_norm, date_modified_norm, 
                date_start_qa, date_load_qa, date_made_qa, date_restart_qa, date_modified_qa, inserted) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisiiisssssssssssiiiisisssssssssssi', 
                $row['claim_id'], $row['claim_norm_id'], $row['web_archive'], $row['user_id_norm'], $row['user_id_qa'], $item, $row['cleaned_claim'], $row['correction_claim'], 
                $row['speaker'], $row['hyperlink'], $row['transcription'], $row['media_source'], $row['check_date'], $row['claim_types'], $row['fact_checker_strategy'], 
                $row['phase_1_label'], $row['phase_2_label'], $row['qa_annotators_num'], $row['qa_skipped'], $valid_annotators_num, $row['num_qapairs'], 
                $row['claim_loc'], $row['latest'], $row['source'], $row['date_start_norm'], $row['date_load_norm'], $row['date_made_norm'], $row['date_restart_norm'], 
                $row['date_modified_norm'], $row['date_start_qa'], $row['date_load_qa'], $row['date_made_qa'], $row['date_restart_qa'], $row['date_modified_qa'], $inserted);
    
                $counter++;
            } 
        update_table($conn, "UPDATE Annotators SET p3_assigned=p3_assigned+? WHERE user_id=?", "ii", $counter, $item);
        echo "Inserted successfully";
        } else {
            while($counter < -$per_user) {
                $sql = "SELECT * FROM Assigned_Valids WHERE valid_annotators_num=0 ORDER BY RAND() LIMIT 1";
                $stmt= $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if (mysqli_num_rows($result)==0) { 
                    break; 
                }

                $row = $result->fetch_assoc();

                update_table($conn, "UPDATE Assigned_Norms SET inserted=0 WHERE claim_norm_id=?", "i", $row['claim_qa_id']);

                update_table($conn, "DELETE FROM Assigned_Valids WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
        
                $counter++;
            }
            update_table($conn, "UPDATE Annotators SET current_valid_task=NULL, p3_assigned=p3_assigned-? WHERE user_id=?", "ii", $counter, $item);
            echo "Removed successfully";
        }
    }
} elseif ($phase == 4) {
    foreach($user_ids as $item){
        $counter = 0;
        if ($per_user > 0) {
            while($counter < $per_user) {
                $sql = "SELECT * FROM Assigned_Valids WHERE inserted=0 AND valid_latest=1 AND phase_2_label != phase_3_label AND user_id_norm!=? AND user_id_qa!=? AND user_id_valid!=? ORDER BY RAND() LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $item, $item, $item);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
    
                $inserted = 0;
                $added_qas = 0;
                $dispute_annotators_num = 0;
    
                update_table($conn, "UPDATE Assigned_Valids SET inserted=1 WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
    
                update_table($conn, "INSERT INTO Assigned_Disputes (claim_id, claim_qa_id, claim_valid_id, web_archive, user_id_norm, user_id_qa, user_id_valid, 
                cleaned_claim, correction_claim, speaker, hyperlink, transcription, media_source, check_date, claim_types, fact_checker_strategy, phase_1_label, 
                phase_2_label, qa_annotators_num, qa_skipped, valid_annotators_num, num_qapairs, claim_loc, latest, source, date_start_norm, date_load_norm, 
                date_made_norm, date_restart_norm, date_modified_norm, date_start_qa, date_load_qa, date_made_qa, date_restart_qa, date_modified_qa, inserted, 
                date_start_valid, date_made_valid, date_restart_valid, date_modified_valid,
                phase_3_label, justification, unreadable, valid_latest, dispute_annotators_num, user_id_dispute, added_qas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                'iiisiiisssssssssssiiiisisssssssssssissssssiiiii', 
                $row['claim_id'], $row['claim_qa_id'], $row['claim_norm_id'], $row['web_archive'], $row['user_id_norm'], $row['user_id_qa'], $row['user_id_valid'], 
                $row['cleaned_claim'], $row['correction_claim'], $row['speaker'], $row['hyperlink'], $row['transcription'], $row['media_source'], $row['check_date'], 
                $row['claim_types'], $row['fact_checker_strategy'], $row['phase_1_label'], $row['phase_2_label'], $row['qa_annotators_num'], $row['qa_skipped'], 
                $row['valid_annotators_num'], $row['num_qapairs'],  $row['claim_loc'], $row['latest'], $row['source'], $row['date_start_norm'], $row['date_load_norm'], 
                $row['date_made_norm'], $row['date_restart_norm'], $row['date_modified_norm'], $row['date_start_qa'], $row['date_load_qa'], $row['date_made_qa'], 
                $row['date_restart_qa'], $row['date_modified_qa'], $inserted, $row['date_start_valid'], $row['date_made_valid'], $row['date_restart_valid'], 
                $row['date_modified_valid'], $row['phase_3_label'], $row['justification'], $row['unreadable'], $row['valid_latest'], $dispute_annotators_num, $item, $added_qas);
    
                $counter++;
            } 
        update_table($conn, "UPDATE Annotators SET p4_assigned=p4_assigned+? WHERE user_id=?", "ii", $counter, $item);
        echo "Inserted successfully";
        } else {
            while($counter < -$per_user) {
                $sql = "SELECT * FROM Assigned_Disputes WHERE dispute_annotators_num=0 ORDER BY RAND() LIMIT 1";
                $stmt= $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
    
                if (mysqli_num_rows($result)==0) { 
                    break; 
                }
    
                $row = $result->fetch_assoc();
    
                update_table($conn, "UPDATE Assigned_Valids SET inserted=0 WHERE claim_norm_id=?", "i", $row['claim_valid_id']);
    
                update_table($conn, "DELETE FROM Assigned_Disputes WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
            
                $counter++;
            }
            update_table($conn, "UPDATE Annotators SET current_dispute_task=NULL, p4_assigned=p4_assigned-? WHERE user_id=?", "ii", $counter, $item);
            echo "Removed successfully";
        }
        
    }
} elseif ($phase == 5) {
    $counter = 0;
    if ($per_user > 0) {
        while($counter < $per_user) {
            $sql = "SELECT * FROM Assigned_Disputes WHERE inserted=0 AND added_qas=1 AND user_id_norm!=? AND user_id_qa!=? AND user_id_valid!=? AND user_id_dispute!=? ORDER BY RAND() LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $item, $item, $item, $item);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
    
            $inserted = 0;
            $post_annotators_num = 0;
    
            update_table($conn, "UPDATE Assigned_Disputes SET inserted=1 WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
    
            update_table($conn, "INSERT INTO Assigned_Posts (claim_id, claim_qa_id, claim_valid_id, claim_dispute_id, web_archive, user_id_norm, user_id_qa, user_id_valid, 
            cleaned_claim, correction_claim, speaker, hyperlink, transcription, media_source, check_date, claim_types, fact_checker_strategy, phase_1_label, 
            phase_2_label, qa_annotators_num, qa_skipped, valid_annotators_num, num_qapairs, claim_loc, latest, source, date_start_norm, date_load_norm, 
            date_made_norm, date_restart_norm, date_modified_norm, date_start_qa, date_load_qa, date_made_qa, date_restart_qa, date_modified_qa, inserted, 
            date_start_valid, date_made_valid, date_restart_valid, date_modified_valid,
            phase_3_label, phase_4_label, justification, unreadable, valid_latest, dispute_annotators_num, added_qas, user_id_dispute, 
            post_annotators_num, user_id_post) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            'iiiisiiisssssssssssiiiisisssssssssssisssssssiiiiiii', 
            $row['claim_id'], $row['claim_qa_id'], $row['claim_valid_id'], $row['claim_norm_id'], $row['web_archive'], $row['user_id_norm'], $row['user_id_qa'], $row['user_id_valid'], 
            $row['cleaned_claim'], $row['correction_claim'], $row['speaker'], $row['hyperlink'], $row['transcription'], $row['media_source'], $row['check_date'], 
            $row['claim_types'], $row['fact_checker_strategy'], $row['phase_1_label'], $row['phase_2_label'], $row['qa_annotators_num'], $row['qa_skipped'], 
            $row['valid_annotators_num'], $row['num_qapairs'],  $row['claim_loc'], $row['latest'], $row['source'], $row['date_start_norm'], $row['date_load_norm'], 
            $row['date_made_norm'], $row['date_restart_norm'], $row['date_modified_norm'], $row['date_start_qa'], $row['date_load_qa'], $row['date_made_qa'], 
            $row['date_restart_qa'], $row['date_modified_qa'], $inserted, $row['date_start_valid'], $row['date_made_valid'], $row['date_restart_valid'], 
            $row['date_modified_valid'], $row['phase_3_label'], $row['phase_4_label'], $row['justification'], $row['unreadable'], $row['valid_latest'], 
            $row['dispute_annotators_num'], $row['added_qas'], $row['user_id_dispute'], $post_annotators_num, $item);
    
            $counter++;
        } 
        update_table($conn, "UPDATE Annotators SET p4_assigned=p4_assigned+? WHERE user_id=?", "ii", $counter, $item);
        echo "Inserted successfully";
    } else {
        while($counter < -$per_user) {
            $sql = "SELECT * FROM Assigned_Posts WHERE post_annotators_num=0 ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
    
                if (mysqli_num_rows($result)==0) { 
                    break; 
                }
    
                $row = $result->fetch_assoc();
    
                update_table($conn, "UPDATE Assigned_Disputes SET inserted=0 WHERE claim_norm_id=?", "i", $row['claim_dispute_id']);
    
                update_table($conn, "DELETE FROM Assigned_Posts WHERE claim_norm_id=?", "i", $row['claim_norm_id']);
            
                $counter++;
            }
            update_table($conn, "UPDATE Annotators SET current_post_task=NULL, p5_assigned=p5_assigned-? WHERE user_id=?", "ii", $counter, $item);
            echo "Removed successfully";
        }
}




$conn->close();
?>