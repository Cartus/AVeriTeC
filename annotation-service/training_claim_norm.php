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

$db_params = parse_ini_file( dirname(__FILE__).'/train_params.ini', false);

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['user_id'];
$req_type = $_POST['req_type'];

if ($req_type == "load-data"){

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT user_id, current_norm_task FROM Annotators WHERE user_id=?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(mysqli_num_rows($result) > 0){
        $row = $result->fetch_assoc();
        if ($row['current_norm_task'] != 0) {
            $sql = "SELECT claim_id, web_archive FROM Assigned_Claims WHERE claim_id=?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_norm_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $output = (["web_archive" => $row['web_archive'], "claim_id" => $row['claim_id']]);
            echo(json_encode($output));

            update_table($conn, "UPDATE Assigned_Claims SET date_start_norm=? WHERE claim_id=?", 'si', $date, $row['claim_id']);

        } else {
            // $sql = "SELECT claim_id, web_archive FROM Assigned_Claims WHERE user_id_norm=? AND norm_annotators_num=0 AND norm_taken_flag=0 AND norm_skipped=0 ORDER BY RAND() LIMIT 1";
            $sql = "SELECT claim_id, web_archive FROM Assigned_Claims WHERE user_id_norm=? AND norm_annotators_num=0 AND norm_skipped=0 ORDER BY RAND() LIMIT 1";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if(mysqli_num_rows($result) > 0) {
                $row = $result->fetch_assoc();
                $output = (["web_archive" => $row['web_archive'], "claim_id" => $row['claim_id']]);
                echo(json_encode($output));

                $conn->begin_transaction();
                try {
                    if(!is_null($row['claim_id'])){
                        // update_table($conn, "UPDATE Assigned_Claims SET norm_taken_flag=1, user_id_norm=?, date_start_norm=? WHERE claim_id=?", 'isi', $user_id, $date, $row['claim_id']);
                        update_table($conn, "UPDATE Assigned_Claims SET user_id_norm=?, date_start_norm=? WHERE claim_id=?", 'isi', $user_id, $date, $row['claim_id']);
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

} 

?>