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

$sql = "DROP TABLE Search_Record";
 
if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// sql to create table
$sql = "CREATE TABLE Search_Record (
search_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_norm_id INT(6) NOT NULL,
user_id_qa INT(6) NOT NULL,
query VARCHAR(500) NOT NULL,
abstract VARCHAR(500) NOT NULL,
header VARCHAR(100) NOT NULL,
problematic VARCHAR(100) NOT NULL,
result_url VARCHAR(500) NOT NULL,
country_code VARCHAR(10) NOT NULL,
date_query DATETIME NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Search_Record created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
