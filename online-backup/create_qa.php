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

$sql = "DROP TABLE Qapair";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Qapair (
qa_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_norm_id INT(6) NOT NULL,
user_id_qa INT(6) NOT NULL,
question VARCHAR(500) NOT NULL,
answer VARCHAR(2500) NOT NULL,
source_url VARCHAR(500),
answer_type VARCHAR(100),
source_medium VARCHAR(100),
answer_problems VARCHAR(500),
bool_explanation VARCHAR(1500),
answer_second VARCHAR(2500),
source_url_second VARCHAR(500),
answer_type_second VARCHAR(100),
source_medium_second VARCHAR(100),
answer_problems_second VARCHAR(500),
bool_explanation_second VARCHAR(1500),
answer_third VARCHAR(2500),
source_url_third VARCHAR(500),
answer_type_third VARCHAR(100),
source_medium_third VARCHAR(100),
answer_problems_third VARCHAR(500),
bool_explanation_third VARCHAR(1500),
question_problems VARCHAR(200),
qa_latest INT(6) NOT NULL,
p4_latest INT(6),
edit_latest INT(6),
date_made DATETIME,
date_modified DATETIME
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Qapair created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>