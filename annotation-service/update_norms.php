<?php

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt = $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$user_id = 5;
// $claim_id = 10;


// update_table($conn, "UPDATE Norm_Claims SET user_id_qa=NULL, qa_taken_flag=0 WHERE user_id_norm=? AND claim_norm_id=?", 'ii', $user_id, $claim_id);
// update_table($conn, "UPDATE Norm_Claims SET latest=1 WHERE user_id_norm=? AND claim_norm_id=?", 'ii', $user_id, $claim_id);

update_table($conn, "DELETE FROM Norm_Claims WHERE latest=0 AND user_id_norm=?", 'i', $user_id);


$conn->close();
?>

