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


// $id = 8;
// $pd = 'user131';
// $pd_md5='d0732364726e6527d17965baa1973f1f';

// $sql = 'UPDATE Annotators SET password_md5="599b60f343e0cd5d7623c0e586b44ab6" WHERE user_id=42';
// update_table($conn, "UPDATE Annotators SET password_cleartext=? AND password_md5=? WHERE user_id=?", 'ssi', $pd, $pd_md5, $id);

// update_table($conn, "UPDATE Annotators SET train_finished_valid_annotations=20 WHERE user_id=?", 'i', $id);

// update_table($conn, "UPDATE Annotators SET p5_assigned=0, finished_post_annotations=0 WHERE user_id=?", 'i', $id);

$sql = 'UPDATE Annotators SET current_norm_task=NULL, finished_norm_annotations=0, skipped_norm_data=0, p1_time_sum=0, p1_load_sum=0, p1_speed_trap=0, p1_timed_out=0, p1_assigned=0 WHERE user_id=8';

if ($conn->query($sql) === TRUE) {
    echo "Table Annotators updated successfully";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?>
