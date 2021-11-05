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
$sql = "CREATE TABLE Norm_Claims (
claim_norm_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_id INT(6) NOT NULL,
web_archive VARCHAR(500) NOT NULL,
user_id_norm INT(6) NOT NULL,
user_id_qa INT(6),
user_id_valid INT(6),
qa_annotators_num INT(6) NOT NULL,
valid_annotators_num INT(6) NOT NULL,
cleaned_claim VARCHAR(500) NOT NULL,
speaker VARCHAR(100),
hyperlink VARCHAR(100),
transcription VARCHAR(100),
media_source VARCHAR(100),
check_date VARCHAR(100),
claim_types VARCHAR(500) NOT NULL,
fact_checker_strategy VARCHAR(500) NOT NULL,
phase_1_label VARCHAR(50) NOT NULL,
claim_loc VARCHAR(50),
phase_2_label VARCHAR(50), 
phase_3_label VARCHAR(50), 
justification VARCHAR(1500),
has_qapairs INT(6) NOT NULL,
num_qapairs INT(6),
qa_taken_flag INT(6) NOT NULL,
qa_skipped INT(6) NOT NULL,
qa_skipped_by INT(6),
valid_taken_flag INT(6) NOT NULL,
date_made_norm DATETIME,
date_modified_norm DATETIME,
date_made_qa DATETIME,
date_modified_qa DATETIME,
date_made_valid DATETIME,
date_modified_valid DATETIME
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Norm_Claims created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>