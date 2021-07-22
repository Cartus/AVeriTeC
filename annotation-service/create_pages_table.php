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
$sql = "CREATE TABLE Pages(
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
refers_to TEXT(50) NOT NULL,
refers_to_fact_checking_site BOOLEAN NOT NULL,
claim_text TEXT(200) NOT NULL,
fact_checking_org TEXT(50) NOT NULL,
fact_checking_article_url TEXT(200) NOT NULL,
fact_checking_article_headline TEXT(200) NOT NULL,
fact_checking_verdict TEXT(50) NOT NULL,
fact_checking_date TEXT(50) NOT NULL,
original_claim_url TEXT(50) NOT NULL,
original_claim_source TEXT(50) NOT NULL,
fact_checking_article_raw_html TEXT(2000) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
echo "Table Claims created successfully";
} else {
echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
