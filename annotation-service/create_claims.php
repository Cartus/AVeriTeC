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
$sql = "CREATE TABLE Claims (
claim_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_text VARCHAR(500) NOT NULL,
web_archive VARCHAR(500) NOT NULL,
norm_annotators_num INT(6) NOT NULL,
user_id_norm INT(6),
norm_taken_flag INT(6) NOT NULL,
norm_skipped INT(6) NOT NULL,
norm_skipped_by INT(6),
date_made_norm DATETIME,
date_modified_norm DATETIME
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Claims created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>