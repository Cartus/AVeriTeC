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

$sql = "DROP TABLE Norm_Claims";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Norm_Claims (
claim_norm_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_id INT(6) NOT NULL,
web_archive VARCHAR(500) NOT NULL,
user_id_norm INT(6) NOT NULL,
cleaned_claim VARCHAR(500) NOT NULL,
correction_claim VARCHAR(500),
speaker VARCHAR(100),
hyperlink VARCHAR(500),
source VARCHAR(500),
transcription VARCHAR(2500),
media_source VARCHAR(500),
check_date VARCHAR(100),
claim_loc VARCHAR(50),
claim_types VARCHAR(500) NOT NULL,
fact_checker_strategy VARCHAR(500) NOT NULL,
phase_1_label VARCHAR(50) NOT NULL,
latest INT(6) NOT NULL,
date_start_norm DATETIME,
date_load_norm DATETIME,
date_made_norm DATETIME,
date_restart_norm DATETIME,
date_modified_norm DATETIME,
nonfactual INT(6) NOT NULL,
inserted INT(6) NOT NULL
)";


if ($conn->query($sql) === TRUE) {
    echo "Table Norm_Claims created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
