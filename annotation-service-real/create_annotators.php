<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DROP TABLE Annotators";

if ($conn->query($sql) === TRUE) {
    echo "Table dropped successfully";
} else {
    echo "Error creating table: " . $conn->error;
}


// sql to create table
$sql = "CREATE TABLE Annotators (
user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_name VARCHAR(255) NOT NULL,
is_admin INT(6) NOT NULL,
password_cleartext VARCHAR(255) NOT NULL,
password_md5 VARCHAR(255) NOT NULL,
number_logins INT(6) NOT NULL,
annotation_phase VARCHAR(20),
current_norm_task INT(6),
current_qa_task INT(6),
current_valid_task INT(6),
finished_norm_annotations INT(6) NOT NULL,
finished_qa_annotations INT(6) NOT NULL,
finished_valid_annotations INT(6) NOT NULL,
skipped_norm_data INT(6) NOT NULL,
skipped_qa_data INT(6) NOT NULL,
skipped_valid_data INT(6),
p1_time_sum float(12),
p1_load_sum float(12),
p2_time_sum float(12),
p2_load_sum float(12),
p3_time_sum float(12),
p3_load_sum float(12),
p1_timed_out INT(6),
p2_timed_out INT(6),
p1_speed_trap INT(6),
p2_speed_trap INT(6),
p3_speed_trap INT(6)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Annotators created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>