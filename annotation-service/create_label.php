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

$sql = "DROP TABLE Label";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Label (
claim_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_valid_id INT(6) NOT NULL,
user_id_dispute INT(6) NOT NULL,
phase_4_label VARCHAR(50) NOT NULL
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Label created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>