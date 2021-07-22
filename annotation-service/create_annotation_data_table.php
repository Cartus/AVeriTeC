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
$sql = "CREATE TABLE ClaimAnnotationData(
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_id INT(6) NOT NULL,
hyperlink TEXT(20) NOT NULL,
date TEXT(20) NOT NULL,
speaker TEXT(10) NOT NULL,
transcription TEXT(100),
claim_type TEXT(10) NOT NULL,
strategy TEXT(10) NOT NULL,
question1 TEXT(50) NOT NULL,
answer1 TEXT(100) NOT NULL,
answer_type1 TEXT(10) NOT NULL,
question2 TEXT(50) NOT NULL,
answer2 TEXT(100) NOT NULL,
answer_type2 TEXT(10) NOT NULL,
question3 TEXT(50) NOT NULL,
answer3 TEXT(100) NOT NULL,
answer_type3 TEXT(10) NOT NULL,
question4 TEXT(50) NOT NULL,
answer4 TEXT(100) NOT NULL,
answer_type4 TEXT(10) NOT NULL,
question5 TEXT(50) NOT NULL,
answer5 TEXT(100) NOT NULL,
answer_type5 TEXT(10) NOT NULL,
skipped INT(6),
skipped_by INT(6),
label_phase1 INT(4) NOT NULL,
label_phase2 INT(4) NOT NULL,
label_phase3 INT(4) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Claims created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
