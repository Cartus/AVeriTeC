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
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
annotator_name VARCHAR(255) NOT NULL,
password_cleartext VARCHAR(255) NOT NULL,
password_md5 VARCHAR(500) NOT NULL,
annotation_mode VARCHAR(30) NOT NULL
)";



if ($conn->query($sql) === TRUE) {
  echo "Table Annotators created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}

$conn->close();
?>