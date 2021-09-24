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
$sql = "CREATE TABLE Claims (
claim_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
claim_text TEXT(500) NOT NULL,
user_id INT(6),
norm_annotators_num INT(10),
source_claim TEXT(100) NOT NULL,
source_claim_url TEXT(100) NOT NULL,
verdict_article TEXT(50) NOT NULL,
url_article TEXT(100) NOT NULL,
url_previous_checker TEXT(100),
transcription TEXT(100),
norm_claim1 TEXT(500),
type_claim1 TEXT(500),
strategy_claim1 TEXT(50),
verdict_claim1 TEXT(50),
norm_claim2 TEXT(500),
type_claim2 TEXT(500),
strategy_claim2 TEXT(50),
verdict_claim2 TEXT(50),
norm_claim3 TEXT(500),
type_claim3 TEXT(500),
strategy_claim3 TEXT(50),
verdict_claim3 TEXT(50),
norm_claim4 TEXT(500),
type_claim4 TEXT(500),
strategy_claim4 TEXT(50),
verdict_claim4 TEXT(50),
norm_claim5 TEXT(500),
type_claim5 TEXT(500),
strategy_claim5 TEXT(50),
verdict_claim5 TEXT(50),
date_claim TEXT(10),
annotation_time_events TEXT(50),
total_annotation_time TEXT(50),
taken_flag BOOLEAN,
skipped TEXT(50),
skipped_by INT(6),
date_made DATETIME,
date_modified DATETIME
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Claims created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
