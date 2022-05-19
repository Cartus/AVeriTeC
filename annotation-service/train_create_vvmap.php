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

$sql = "DROP TABLE VV_Map";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// sql to create table
$sql = "CREATE TABLE VV_Map (
map_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_id INT(6),
claim_id INT(6),
date_made DATETIME,
date_modified DATETIME,
phase_3_label VARCHAR(50),
justification VARCHAR(2500),
unreadable INT(6)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table VV_Map created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>