<?php
date_default_timezone_set('UTC');
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
$phase = $_POST['phase'];

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($phase == 1) {
    $claim_id = $_POST['claim_id'];
    update_table($conn, "UPDATE Assigned_Claims SET date_load_norm=? WHERE claim_id=?", 'si', $date, $claim_id);
    echo "Finished Loading!";
} elseif ($phase == 2) {
    $claim_norm_id = $_POST['claim_norm_id'];
    update_table($conn, "UPDATE Assigned_Norms SET date_load_cache_qa=? WHERE claim_norm_id=?",'si', $date, $claim_norm_id);
    echo "Finished Loading!";
} elseif ($phase == 4) {
    $claim_norm_id = $_POST['claim_norm_id'];
    update_table($conn, "UPDATE Assigned_Disputes SET date_load_cache_dispute=? WHERE claim_norm_id=?",'si', $date, $claim_norm_id);
    echo "Finished Loading!";
}



?>