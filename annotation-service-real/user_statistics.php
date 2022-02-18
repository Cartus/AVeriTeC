<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');


$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['logged_in_user_id'];
// $req_type = $_POST['req_type'];

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Annotators WHERE user_id = ?";
$stmt= $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows > 0) {
    $average_task1_time =  round($row['p1_time_sum'] / $$row['finished_norm_annotations'], 2);
    $average_task2_time =  round($row['p2_time_sum'] / $$row['finished_qa_annotations'], 2);
    $average_task3_time =  round($row['p3_time_sum'] / $$row['finished_valid_annotations'], 2);

    $average_task1_load =  round($row['p1_load_sum'] / $$row['finished_norm_annotations'], 2);
    $average_task2_load =  round($row['p2_load_sum'] / $$row['finished_qa_annotations'], 2);
    $average_task3_load =  round($row['p3_load_sum'] / $$row['finished_valid_annotations'], 2);

    $phase_1 = (["annotations_done" => $row['finished_norm_annotations'], "claims_skipped" => $row['skipped_norm_data'], "annotations_timed_out" => $row['p1_timed_out'], "speed_traps_hit" => $row['p1_speed_trap'], "average_task_time" => $average_task1_time, "average_load_time" => $average_task1_load]);
    $phase_2 = (["annotations_done" => $row['finished_qa_annotations'], "claims_skipped" => $row['skipped_qa_data'], "annotations_timed_out" => $row['p2_timed_out'], "speed_traps_hit" => $row['p2_speed_trap'], "average_task_time" => $average_task2_time, "average_load_time" => $average_task2_load]);
    $phase_3 = (["annotations_done" => $row['finished_valid_annotations'], "claims_skipped" => $row['skipped_valid_data'], "speed_traps_hit" => $row['p3_speed_trap'], "average_task_time" => $average_task3_time, "average_load_time" => $average_task3_load]);
    $output = (["username" => $row['user_name'], "phase_1" => $phase_1, "phase_2" => $phase_2, "phase_3" => $phase_3]);
    echo(json_encode($output));
} else {
    echo "0 Results";
}


$conn->close();

?>