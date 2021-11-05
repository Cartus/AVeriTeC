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
$sql = "CREATE TABLE Claim_Map (
map_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_id INT(6) NOT NULL,
claim_id INT(6) NOT NULL,
skipped INT(6) NOT NULL,
date_made DATETIME NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Claim_Map created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>