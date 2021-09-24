<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$servername = "localhost";
$dbname = $db_params['database'];

// Create connection
$conn = new mysqli($servername, $db_params['user'], $db_params['password'], $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE Annotators (
user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(255) NOT NULL,
password_cleartext VARCHAR(255) NOT NULL,
password_md5 VARCHAR(255) NOT NULL,
annotation_phase VARCHAR(20) NOT NULL,
current_task INT(10) NOT NULL,
finished_norm_annotations INT(6) NOT NULL,
finished_qa_annotations INT(6) NOT NULL,
finished_valid_annotations INT(6) NOT NULL,
number_logins INT(6) NOT NULL,
annotation_time INT(6) NOT NULL,
skipped_data INT(6) NOT NULL,
reported_claims INT(6) NOT NULL,
active INT(6) NOT NULL                         
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Annotators created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>