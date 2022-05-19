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

$sql = "DROP TABLE Qaproblem";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Qaproblem (
problem_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
qa_id INT(6),
claim_norm_id INT(6),
user_id_qa INT(6),
question_problems VARCHAR(200),
answer_problems VARCHAR(500),
answer_problems_second VARCHAR(500),
answer_problems_third VARCHAR(500),
date_made DATETIME
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Qaproblem created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>