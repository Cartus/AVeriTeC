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


// $user_id = 3;
// $claim_norm_id = 55;
// $question = 'sth'; 
// $answer = 'sth';
// $source_url = NULL; 
// $answer_type = NULL; 
// $source_medium = NULL;

// $user_id = 1;
// $claim_id = 1;
// $skipped = 0;
// $date = date("Y-m-d H:i:s");

$qa_id = 21;
$user_id_qa = 3;

update_table($conn, "UPDATE Qapair SET claim_norm_id=57 WHERE qa_id=? AND user_id_qa=?", 'ii', $qa_id, $user_id_qa);
// update_table($conn, "DELETE FROM Qapair WHERE qa_id=? AND claim_norm_id=?", 'ii', $qa_id, $claim_norm_id);
// update_table($conn, "INSERT INTO Claim_Map (user_id, claim_id, skipped, date_made) VALUES (?, ?, ?, ?)", 'iiis', $user_id, $claim_id, $skipped, $date);

// update_table($conn, "INSERT INTO Qapair (claim_norm_id, user_id_qa, question, answer, source_url, answer_type, source_medium)
//             VALUES (?, ?, ?, ?, ?, ?, ?)", 'iisssss', $claim_norm_id, $user_id, $question, $answer, $source_url, $answer_type, $source_medium);

echo "reset current task!";

$conn->close();
?>
