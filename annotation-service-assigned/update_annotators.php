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


$id = 3;


//update_table($conn, "UPDATE Annotators SET current_norm_task=NULL WHERE user_id=?", 'i', $id);

update_table($conn, "UPDATE Annotators SET p3_assigned=0, finished_valid_annotations=0 WHERE user_id=?", 'i', $id);

$conn->close();
?>
