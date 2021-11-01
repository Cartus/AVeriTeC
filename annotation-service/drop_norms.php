<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DROP TABLE Norm_Claims";
 
if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
 
$conn->close();
?>
