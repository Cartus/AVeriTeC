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

    $sql = "SELECT user_id, current_qa_task, finished_qa_annotations FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_qa_task'] != 0) {
            $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, source, check_date, claim_loc FROM Assigned_Norms WHERE claim_norm_id=?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_qa_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
            "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id']]);
            echo(json_encode($output));
            update_table($conn, "UPDATE Assigned_Norms SET date_start_qa=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);

        } else {
            $sql = "SELECT claim_norm_id, web_archive, cleaned_claim, speaker, source, check_date, claim_loc, user_id_norm FROM Assigned_Norms
            WHERE user_id_qa=? AND qa_annotators_num=0 AND qa_skipped=0 ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $conn->begin_transaction();
            try {
                if(mysqli_num_rows($result) > 0) {
                    $row = $result->fetch_assoc();
                    $output = (["web_archive" => $row['web_archive'], "cleaned_claim" => $row['cleaned_claim'], "speaker" => $row['speaker'], "claim_source" => $row['source'],
                    "check_date" => $row['check_date'], "country_code" => $row['claim_loc'], "claim_norm_id" => $row['claim_norm_id'], "user_id_norm" => $row['user_id_norm']]);
                    echo(json_encode($output));

                    update_table($conn, "UPDATE Assigned_Norms SET date_start_qa=? WHERE claim_norm_id=?", 'si', $date, $row['claim_norm_id']);
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
} 

?>
