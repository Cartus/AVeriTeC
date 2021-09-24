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
$sql = "CREATE TABLE Qapairs (
claim_norm_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_norm_text TEXT(500) NOT NULL,
url_article TEXT(100) NOT NULL,
verdict TEXT(50) NOT NULL,
qa_user_id INT(6),
valid_user_id INT(6),
qa_annotators_num INT(10),
valid_annotators_num INT(10),
validated_verdict TEXT(50),
question1 TEXT(500),
answer1 TEXT(2000),
type_answer1 TEXT(50),
url_answer1 TEXT(100),
incorrect_answer1 BOOLEAN,
question2 TEXT(500),
answer2 TEXT(2000),
type_answer2 TEXT(50),
url_answer2 TEXT(100),
incorrect_answer2 BOOLEAN,
question3 TEXT(500),
answer3 TEXT(2000),
type_answer3 TEXT(50),
url_answer3 TEXT(100),
incorrect_answer3 BOOLEAN,
question4 TEXT(500),
answer4 TEXT(2000),
type_answer4 TEXT(50),
url_answer4 TEXT(100),
incorrect_answer4 BOOLEAN,
question5 TEXT(500),
answer5 TEXT(2000),
type_answer5 TEXT(50),
url_answer5 TEXT(100),
incorrect_answer5 BOOLEAN,
taken_flag BOOLEAN,
valid_taken_flag BOOLEAN,
skipped TEXT(500),
skipped_by INT(6),
valid_skipped TEXT(500),
valid_skipped_by INT(6),
total_annotation_time TEXT(100),
date_made DATETIME,
date_modified DATETIME
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Evidence created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>