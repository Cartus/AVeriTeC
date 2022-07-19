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

$gold_array = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // echo "user_id: " . $row["user_id"]. "<br>";
        // echo "user_name: " . $row["user_name"]. "<br>";
        // echo "is_admin: " . $row["is_admin"]. "<br>";
        // echo "is_active: " . $row["is_active"]. "<br>";
        // echo "password: " . $row["password_cleartext"]. "<br>";
        // echo "current_norm_task: " . $row["current_norm_task"]. "<br>";
        // echo "current_qa_task: " . $row["current_qa_task"]. "<br>";
        // echo "current_valid_task: " . $row["current_valid_task"]. "<br>";
        // echo "current_dispute_task: " . $row["current_dispute_task"]. "<br>";
        // echo "current_post_task: " . $row["current_post_task"]. "<br>";
        // echo "train_current_norm_task: " . $row["train_current_norm_task"]. "<br>";
        // echo "train_current_qa_task: " . $row["train_current_qa_task"]. "<br>";
        // echo "train_current_valid_task: " . $row["train_current_valid_task"]. "<br>";
        // echo "finished_norm_annotations: " . $row["finished_norm_annotations"]. "<br>";
        // echo "finished_qa_annotations: " . $row["finished_qa_annotations"]. "<br>";
        // echo "finished_valid_annotations: " . $row["finished_valid_annotations"]. "<br>";
        // echo "finished_dispute_annotations: " . $row["finished_dispute_annotations"]. "<br>";
        // echo "finished_post_annotations: " . $row["finished_post_annotations"]. "<br>";
        // echo "train_finished_norm_annotations: " . $row["train_finished_norm_annotations"]. "<br>";
        // echo "train_finished_qa_annotations: " . $row["train_finished_qa_annotations"]. "<br>";
        // echo "train_finished_valid_annotations: " . $row["train_finished_valid_annotations"]. "<br>";
        // echo "questions_p2: " . $row["questions_p2"]. "<br>";
        // echo "questions_p4: " . $row["questions_p4"]. "<br>";
        // echo "skipped_norm_data: " . $row["skipped_norm_data"]. "<br>";
        // echo "skipped_qa_data: " . $row["skipped_qa_data"]. "<br>";
        // echo "number_logins: " . $row["number_logins"]. "<br>";
        // echo "p1_time_sum: " . $row["p1_time_sum"]. "<br>";
        // echo "p1_load_sum: " . $row["p1_load_sum"]. "<br>";
        // echo "p2_time_sum: " . $row["p2_time_sum"]. "<br>";
        // echo "p2_load_sum: " . $row["p2_load_sum"]. "<br>";
        // echo "p3_time_sum: " . $row["p3_time_sum"]. "<br>";
        // echo "p4_load_sum: " . $row["p4_load_sum"]. "<br>";
        // echo "p4_time_sum: " . $row["p4_time_sum"]. "<br>";
        // echo "p5_time_sum: " . $row["p5_time_sum"]. "<br>";
        // echo "p1_timed_out: " . $row["p1_timed_out"]. "<br>";
        // echo "p2_timed_out: " . $row["p2_timed_out"]. "<br>";
        // echo "p4_timed_out: " . $row["p4_timed_out"]. "<br>";
        // echo "p1_speed_trap: " . $row["p1_speed_trap"]. "<br>";
        // echo "p2_speed_trap: " . $row["p2_speed_trap"]. "<br>";
        // echo "p3_speed_trap: " . $row["p3_speed_trap"]. "<br>";
        // echo "p4_speed_trap: " . $row["p4_speed_trap"]. "<br>";
        // echo "p5_speed_trap: " . $row["p5_speed_trap"]. "<br>";
        // echo "p1_assigned: " . $row["p1_assigned"]. "<br>";
        // echo "p2_assigned: " . $row["p2_assigned"]. "<br>";
        // echo "p3_assigned: " . $row["p3_assigned"]. "<br>";
        // echo "p4_assigned: " . $row["p4_assigned"]. "<br>";
        // echo "p5_assigned: " . $row["p5_assigned"]. "<br>";
        // echo "train_p1_assigned: " . $row["train_p1_assigned"]. "<br>";
        // echo "train_p2_assigned: " . $row["train_p2_assigned"]. "<br>";
        // echo "train_p3_assigned: " . $row["train_p3_assigned"]. "<br>";
        // echo "<br>";

        array_push($gold_array, ["user_id" => $row['user_id'], "user_name" => $row['user_name'], "password_cleartext" => $row['password_cleartext'], "password_md5" => $row['password_md5'], "is_admin" => $row['is_admin'], "is_active" => $row['is_active'], "current_norm_task" => $row['current_norm_task'], "current_qa_task" => $row['current_qa_task'], 
        "current_valid_task" => $row['current_valid_task'], "current_dispute_task" => $row['current_dispute_task'], "current_post_task" => $row['current_post_task'], "train_current_norm_task" => $row['train_current_norm_task'],
        "train_current_qa_task" => $row['train_current_qa_task'], "train_current_valid_task" => $row['train_current_valid_task'], "finished_norm_annotations" => $row['finished_norm_annotations'], "finished_qa_annotations" => $row['finished_qa_annotations'], "finished_valid_annotations" => $row['finished_valid_annotations'],
        "finished_dispute_annotations" => $row['finished_dispute_annotations'], "finished_post_annotations" => $row['finished_post_annotations'], "train_finished_norm_annotations" => $row['train_finished_norm_annotations'], "train_finished_qa_annotations" => $row['train_finished_qa_annotations'], 
        "train_finished_valid_annotations" => $row['train_finished_valid_annotations'], "questions_p2" => $row['questions_p2'], "questions_p4" => $row['questions_p4'], "skipped_norm_data" => $row['skipped_norm_data'], "skipped_qa_data" => $row['skipped_qa_data'], 
        "number_logins" => $row['number_logins'], "p1_time_sum" => $row['p1_time_sum'], "p1_load_sum" => $row['p1_load_sum'], "p2_time_sum" => $row['p2_time_sum'], "p2_load_sum" => $row['p2_load_sum'], "p3_time_sum" => $row['p3_time_sum'], "p4_load_sum" => $row['p4_load_sum'], 
        "p4_time_sum" => $row['p4_time_sum'], "p5_time_sum" => $row['p5_time_sum'], "p1_timed_out" => $row['p1_timed_out'], "p2_timed_out" => $row['p2_timed_out'], "p4_timed_out" => $row['p4_timed_out'], "p1_speed_trap" => $row['p1_speed_trap'], "p2_speed_trap" => $row['p2_speed_trap'], 
        "p3_speed_trap" => $row['p3_speed_trap'], "p4_speed_trap" => $row['p4_speed_trap'], "p5_speed_trap" => $row['p5_speed_trap'], "p1_assigned" => $row['p1_assigned'], "p2_assigned" => $row['p2_assigned'], "p3_assigned" => $row['p3_assigned'], 
        "p4_assigned" => $row['p4_assigned'], "p5_assigned" => $row['p5_assigned'], "train_p1_assigned" => $row['train_p1_assigned'], "train_p2_assigned" => $row['train_p2_assigned'], "train_p3_assigned" => $row['train_p3_assigned']]);
    }
} else {
    echo "0 Results";
}

$json = json_encode($gold_array);

//write json to file
if (file_put_contents("results/annotators.json", $json))
    echo "JSON file created successfully...";
else 
    echo "Oops! Error creating json file...";

$conn->close();
?>

