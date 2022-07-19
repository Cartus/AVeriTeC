<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');


$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['get_by_user_id'];
$req_type = $_POST['req_type'];
$log_id = $_POST['logged_in_user_id'];

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT is_admin FROM Annotators WHERE user_id=?";
$stmt= $conn->prepare($sql);
$stmt->bind_param("i", $log_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$is_admin = $row['is_admin'];

$sql = "SELECT * FROM Annotators WHERE user_id=?";
$stmt= $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
// print_r($row);

if ($result->num_rows > 0) {
    $average_task1_time =  round($row['p1_time_sum'] / max($row['finished_norm_annotations'], 1), 2);
    $average_task2_time =  round($row['p2_time_sum'] / max($row['finished_qa_annotations'], 1), 2);
    $average_task3_time =  round($row['p3_time_sum'] / max($row['finished_valid_annotations'], 1), 2);
    $average_task4_time =  round($row['p4_time_sum'] / max($row['finished_dispute_annotations'], 1), 2);
    $average_task5_time =  round($row['p5_time_sum'] / max($row['finished_post_annotations'], 1), 2);

    $average_task1_load =  round($row['p1_load_sum'] / max($row['finished_norm_annotations'], 1), 2);
    $average_task2_load =  round($row['p2_load_sum'] / max($row['finished_qa_annotations'], 1), 2);
    $average_task4_load =  round($row['p4_load_sum'] / max($row['finished_dispute_annotations'], 1), 2);

    $p1_skipped_percent =  round($row['skipped_norm_data'] / max($row['finished_norm_annotations'], 1), 2);
    $p2_skipped_percent =  round($row['skipped_qa_data'] / max($row['finished_qa_annotations'], 1), 2);

    $average_questions_p2 = round($row['questions_p2'] / max($row['finished_qa_annotations'], 1), 2);
    $average_questions_p4 = round($row['questions_p4'] / max($row['finished_dispute_annotations'], 1), 2);

    $phase_1 = (["training_annotations_done" => $row['train_finished_norm_annotations'], "training_annotations_assigned" => $row['train_p1_assigned'],
        "annotations_done" => $row['finished_norm_annotations'], "annotations_assigned" => $row['p1_assigned'], "skipped_claims_percentage" => $p1_skipped_percent,
        "skipped_claims" => $row['skipped_norm_data'], "annotations_timed_out" => $row['p1_timed_out'], "speed_traps_hit" => $row['p1_speed_trap'], "average_task_time" => $average_task1_time, "average_load_time" => $average_task1_load]);

    $phase_2 = (["training_annotations_done" => $row['train_finished_qa_annotations'], "training_annotations_assigned" => $row['train_p2_assigned'],
        "average_questions" => $average_questions_p2, "annotations_done" => $row['finished_qa_annotations'], "annotations_assigned" => $row['p2_assigned'], "skipped_claims_percentage" => $p2_skipped_percent,
        "skipped_claims" => $row['skipped_qa_data'], "annotations_timed_out" => $row['p2_timed_out'], "speed_traps_hit" => $row['p2_speed_trap'], "average_task_time" => $average_task2_time, "average_load_time" => $average_task2_load]);

    $phase_3 = (["training_annotations_done" => $row['train_finished_valid_annotations'], "training_annotations_assigned" => $row['train_p3_assigned'],
        "annotations_done" => $row['finished_valid_annotations'], "annotations_assigned" => $row['p3_assigned'], "speed_traps_hit" => $row['p3_speed_trap'], "average_task_time" => $average_task3_time]);

    $phase_4 = (["average_questions" => $average_questions_p4, "annotations_done" => $row['finished_dispute_annotations'], "annotations_assigned" => $row['p4_assigned'], "annotations_timed_out" => $row['p4_timed_out'], "speed_traps_hit" => $row['p4_speed_trap'], "average_task_time" => $average_task4_time, "average_load_time" => $average_task4_load]);

    $phase_5 = (["annotations_done" => $row['finished_post_annotations'], "annotations_assigned" => $row['p5_assigned'], "speed_traps_hit" => $row['p5_speed_trap'], "average_task_time" => $average_task5_time]);

    $output = (["username" => $row['user_name'], "is_admin" => $is_admin, "phase_1" => $phase_1, "phase_2" => $phase_2, "phase_3" => $phase_3, "phase_4" => $phase_4, "phase_5" => $phase_5]);

    echo(json_encode($output));


} else {
    echo "0 Results";
}


$conn->close();

?>