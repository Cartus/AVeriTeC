<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE Annotators (
user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(255) NOT NULL,
is_admin INT(6) NOT NULL,
password_cleartext VARCHAR(255) NOT NULL,
password_md5 VARCHAR(255) NOT NULL,
number_logins INT(6) NOT NULL,
annotation_phase VARCHAR(20),
current_norm_task INT(6),
current_qa_task INT(6),
current_valid_task INT(6),
finished_norm_annotations INT(6) NOT NULL,
finished_qa_annotations INT(6) NOT NULL,
finished_valid_annotations INT(6) NOT NULL,
annotation_norm_time INT(6),
skipped_norm_data INT(6),
reported_norm_claims INT(6),
skipped_qa_data INT(6),
reported_qa_claims INT(6),
skipped_valid_data INT(6),
reported_valid_claims INT(6),
active INT(6)                         
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Annotators created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>