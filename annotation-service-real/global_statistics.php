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


$sql = "SELECT COUNT(*) FROM Annotators WHERE finished_norm_annotations>0";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$active_users_p1 = $row['COUNT(*)'];
// echo $active_users_p1;

$sql = "SELECT COUNT(*) FROM Annotators WHERE finished_qa_annotations>0";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$active_users_p2 = $row['COUNT(*)'];
// echo $active_users_p2;

$sql = "SELECT COUNT(*) FROM Annotators WHERE finished_valid_annotations>0";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$active_users_p3 = $row['COUNT(*)'];
// echo $active_users_p3;

$sql = "SELECT SUM(p1_time_sum), SUM(p2_time_sum), SUM(p3_time_sum), SUM(p1_load_sum), SUM(p2_load_sum),
        SUM(finished_norm_annotations), SUM(finished_qa_annotations), SUM(finished_valid_annotations),
        SUM(skipped_norm_data), SUM(skipped_qa_data), SUM(skipped_valid_data),
        SUM(p1_timed_out), SUM(p2_timed_out), SUM(p1_speed_trap), SUM(p2_speed_trap), SUM(p3_speed_trap)
        FROM Annotators";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows > 0) {
    $p1_annotations_done = round($row['SUM(finished_norm_annotations)'] / max($active_users_p1, 1), 2);
    $p2_annotations_done = round($row['SUM(finished_qa_annotations)'] / max($active_users_p2, 1), 2);
    $p3_annotations_done = round($row['SUM(finished_valid_annotations)'] / max($active_users_p3, 1), 2);

    $p1_claims_skipped = round($row['SUM(skipped_norm_data)'] / max($active_users_p1, 1), 2);
    $p2_claims_skipped = round($row['SUM(skipped_qa_data)'] / max($active_users_p2, 1), 2);
    $p3_claims_skipped = round($row['SUM(skipped_valid_data)'] / max($active_users_p3, 1), 2);

    $p1_timed_out = round($row['SUM(p1_timed_out)'] / max($active_users_p1, 1), 2);
    $p2_timed_out = round($row['SUM(p2_timed_out)'] / max($active_users_p2, 1), 2);

    $p1_speed_trap = round($row['SUM(p1_speed_trap)'] / max($active_users_p1, 1), 2);
    $p2_speed_trap = round($row['SUM(p2_speed_trap)'] / max($active_users_p2, 1), 2);
    $p3_speed_trap = round($row['SUM(p3_speed_trap)'] / max($active_users_p3, 1), 2);

    $p1_average_task_time = round($row['SUM(p1_time_sum)'] / max($row['SUM(finished_norm_annotations)'], 1), 2);
    $p2_average_task_time = round($row['SUM(p2_time_sum)'] / max($row['SUM(finished_qa_annotations)'], 1), 2);
    $p3_average_task_time = round($row['SUM(p3_time_sum)'] / max($row['SUM(finished_valid_annotations)'], 1), 2);

    $p1_average_load_time = round($row['SUM(p1_load_sum)'] / max($row['SUM(finished_norm_annotations)'], 1), 2);
    $p2_average_load_time = round($row['SUM(p2_load_sum)'] / max($row['SUM(finished_qa_annotations)'], 1), 2);

    $phase1 = (["annotations_assigned" => 10, "speed_traps_hit" => $p1_speed_trap, "annotations_timed_out" => $p1_timed_out, "claims_skipped" => $p1_claims_skipped, "annotations_done" => $p1_annotations_done, "average_load_time" => $p1_average_load_time, "average_task_time" => $p1_average_task_time]);
    $phase2 = (["annotations_assigned" => 10, "speed_traps_hit" => $p2_speed_trap, "annotations_timed_out" => $p2_timed_out, "claims_skipped" => $p2_claims_skipped, "annotations_done" => $p2_annotations_done, "average_load_time" => $p2_average_load_time, "average_task_time" => $p2_average_task_time]);
    $phase3 = (["annotations_assigned" => 10, "speed_traps_hit" => $p3_speed_trap, "claims_skipped" => $p3_claims_skipped, "annotations_done" => $p3_annotations_done, "average_task_time" => $p3_average_task_time]);
    $output = (["phase_1" => $phase1, "phase_2" => $phase2, "phase_3" => $phase3]);
    echo(json_encode($output));
} else {
    echo "0 Results";
}

$conn->close();

?>