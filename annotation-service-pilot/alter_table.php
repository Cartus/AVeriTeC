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
$sql = "ALTER TABLE Norm_Claims
ADD nonfactual INT(6);";

if ($conn->query($sql) === TRUE) {
    echo "Table altered successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>