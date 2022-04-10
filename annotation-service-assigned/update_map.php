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


$user_id = 1;
$claim_id = 9;
//$skipped = 0;
//$date = date("Y-m-d H:i:s");

// update_table($conn, "UPDATE Claim_Map SET skipped=0, claim_id=? WHERE user_id=?", 'ii', $claim_id, $user_id);
update_table($conn, "DELETE FROM Claim_Map WHERE claim_id=? AND user_id=?", 'ii', $claim_id, $user_id);
// update_table($conn, "INSERT INTO Claim_Map (user_id, claim_id, skipped, date_made) VALUES (?, ?, ?, ?)", 'iiis', $user_id, $claim_id, $skipped, $date);

echo "reset current task!";

$conn->close();
?>
