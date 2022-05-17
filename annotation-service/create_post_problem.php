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

$sql = "DROP TABLE Post_Problem";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Post_Problem (
pp_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
qa_id INT(6) NOT NULL,
claim_norm_id INT(6) NOT NULL,
user_id_post INT(6) NOT NULL,
answer_problems VARCHAR(500),
answer_problems_second VARCHAR(500),
answer_problems_third VARCHAR(500),
question_problems VARCHAR(200)
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Post_Problem created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>