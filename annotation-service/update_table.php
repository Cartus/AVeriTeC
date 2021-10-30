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


$id = 1;

// update_table($conn, "UPDATE Annotators SET current_qa_task=0 WHERE user_id=?", 'i', $id);

// update_table($conn, "UPDATE Norm_Claims SET valid_annotators_num=0 WHERE claim_norm_id=?", 'i', $id);

update_table($conn, "UPDATE Claims SET user_id_norm=0, norm_taken_flag=0  WHERE claim_id=?", 'i', $id);


echo "reset current task!";

$conn->close();
?>