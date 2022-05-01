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

$sql = "DROP TABLE Assigned_Posts";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Assigned_Posts (
claim_norm_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_id INT(6) NOT NULL,
claim_qa_id INT(6) NOT NULL,
claim_valid_id INT(6) NOT NULL,
claim_dispute_id INT(6) NOT NULL,
web_archive VARCHAR(500) NOT NULL,
user_id_norm INT(6) NOT NULL,
user_id_qa INT(6) NOT NULL,
user_id_valid INT(6) NOT NULL,
user_id_dispute INT(6) NOT NULL,
user_id_post INT(6) NOT NULL,
qa_annotators_num INT(6) NOT NULL,
valid_annotators_num INT(6) NOT NULL,
dispute_annotators_num INT(6) NOT NULL,
post_annotators_num INT(6) NOT NULL,
cleaned_claim VARCHAR(500) NOT NULL,
correction_claim VARCHAR(500),
speaker VARCHAR(100),
hyperlink VARCHAR(500),
source VARCHAR(500),
transcription VARCHAR(2500),
media_source VARCHAR(500),
check_date VARCHAR(100),
claim_types VARCHAR(500) NOT NULL,
fact_checker_strategy VARCHAR(500) NOT NULL,
claim_loc VARCHAR(50),
phase_1_label VARCHAR(50) NOT NULL,
phase_2_label VARCHAR(50),
phase_3_label VARCHAR(50),
phase_4_label VARCHAR(50),
phase_5_label VARCHAR(50),
justification VARCHAR(1500),
justification_p5 VARCHAR(1500),
num_qapairs INT(6) NOT NULL,
unreadable INT(6),
qa_skipped INT(6) NOT NULL,
qa_skipped_by INT(6),
latest INT(6) NOT NULL,
valid_latest INT(6) NOT NULL,
added_qas INT(6) NOT NULL,
post_latest INT(6) NOT NULL,
date_start_norm DATETIME,
date_load_norm DATETIME,
date_made_norm DATETIME,
date_restart_norm DATETIME,
date_modified_norm DATETIME,
date_start_qa DATETIME,
date_load_qa DATETIME,
date_made_qa DATETIME,
date_restart_qa DATETIME,
date_restart_cache_qa DATETIME,
date_load_cache_qa DATETIME,
date_modified_qa DATETIME,
date_start_valid DATETIME,
date_made_valid DATETIME,
date_restart_valid DATETIME,
date_modified_valid DATETIME,
date_start_dispute DATETIME,
date_load_dispute DATETIME,
date_made_dispute DATETIME,
date_restart_dispute DATETIME,
date_restart_cache_dispute DATETIME,
date_load_cache_dispute DATETIME,
date_modified_dispute DATETIME,
date_start_post DATETIME,
date_made_post DATETIME,
date_restart_post DATETIME,
date_modified_post DATETIME,
inserted INT(6) NOT NULL
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Assigned_Posts created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>