<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Annotators";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "user_id: " . $row["user_id"]. "<br>";
        echo "user_name: " . $row["user_name"]. "<br>";
        echo "is_admin: " . $row["is_admin"]. "<br>";
        echo "is_active: " . $row["is_active"]. "<br>";
        echo "password: " . $row["password_cleartext"]. "<br>";
        echo "current_norm_task: " . $row["current_norm_task"]. "<br>";
        echo "current_qa_task: " . $row["current_qa_task"]. "<br>";
        echo "current_valid_task: " . $row["current_valid_task"]. "<br>";
        echo "current_dispute_task: " . $row["current_dispute_task"]. "<br>";
        echo "current_post_task: " . $row["current_post_task"]. "<br>";
        echo "finished_norm_annotations: " . $row["finished_norm_annotations"]. "<br>";
        echo "finished_qa_annotations: " . $row["finished_qa_annotations"]. "<br>";
        echo "finished_valid_annotations: " . $row["finished_valid_annotations"]. "<br>";
        echo "finished_dispute_annotations: " . $row["finished_dispute_annotations"]. "<br>";
        echo "finished_post_annotations: " . $row["finished_post_annotations"]. "<br>";
        echo "questions_p2: " . $row["questions_p2"]. "<br>";
        echo "questions_p4: " . $row["questions_p4"]. "<br>";
        echo "skipped_norm_data: " . $row["skipped_norm_data"]. "<br>";
        echo "skipped_qa_data: " . $row["skipped_qa_data"]. "<br>";
        echo "number_logins: " . $row["number_logins"]. "<br>";
        echo "p1_time_sum: " . $row["p1_time_sum"]. "<br>";
        echo "p1_load_sum: " . $row["p1_load_sum"]. "<br>";
        echo "p2_time_sum: " . $row["p2_time_sum"]. "<br>";
        echo "p2_load_sum: " . $row["p2_load_sum"]. "<br>";
        echo "p3_time_sum: " . $row["p3_time_sum"]. "<br>";
        echo "p4_load_sum: " . $row["p4_load_sum"]. "<br>";
        echo "p4_time_sum: " . $row["p4_time_sum"]. "<br>";
        echo "p5_time_sum: " . $row["p5_time_sum"]. "<br>";
        echo "p1_timed_out: " . $row["p1_timed_out"]. "<br>";
        echo "p2_timed_out: " . $row["p2_timed_out"]. "<br>";
        echo "p4_timed_out: " . $row["p4_timed_out"]. "<br>";
        echo "p1_speed_trap: " . $row["p1_speed_trap"]. "<br>";
        echo "p2_speed_trap: " . $row["p2_speed_trap"]. "<br>";
        echo "p3_speed_trap: " . $row["p3_speed_trap"]. "<br>";
        echo "p4_speed_trap: " . $row["p4_speed_trap"]. "<br>";
        echo "p5_speed_trap: " . $row["p5_speed_trap"]. "<br>";
        echo "p1_assigned: " . $row["p1_assigned"]. "<br>";
        echo "p2_assigned: " . $row["p2_assigned"]. "<br>";
        echo "p3_assigned: " . $row["p3_assigned"]. "<br>";
        echo "p4_assigned: " . $row["p4_assigned"]. "<br>";
        echo "p5_assigned: " . $row["p5_assigned"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>
