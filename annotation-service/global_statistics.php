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

$sql = "SELECT COUNT(*) FROM Annotators WHERE finished_dispute_annotations>0";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$active_users_p4 = $row['COUNT(*)'];
// echo $active_users_p3;

$sql = "SELECT COUNT(*) FROM Annotators WHERE finished_post_annotations>0";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$active_users_p5 = $row['COUNT(*)'];
// echo $active_users_p3;

$sql = "SELECT SUM(p1_time_sum), SUM(p2_time_sum), SUM(p3_time_sum), SUM(p4_time_sum), SUM(p5_time_sum),
        SUM(p1_load_sum), SUM(p2_load_sum), SUM(p4_load_sum),
        SUM(finished_norm_annotations), SUM(finished_qa_annotations), SUM(finished_valid_annotations),
        SUM(finished_dispute_annotations), SUM(finished_post_annotations),
        SUM(skipped_norm_data), SUM(skipped_qa_data),
        SUM(p1_timed_out), SUM(p2_timed_out), SUM(p4_timed_out),
        SUM(p1_speed_trap), SUM(p2_speed_trap), SUM(p3_speed_trap), SUM(p4_speed_trap), SUM(p5_speed_trap),
        SUM(p1_assigned), SUM(p2_assigned), SUM(p3_assigned), SUM(p4_assigned), SUM(p5_assigned),
        SUM(questions_p2), SUM(questions_p4)
        FROM Annotators";

$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$pending_claims_p1 = 0;
$sql_p1 = "SELECT COUNT(*) FROM Claims WHERE inserted=0";
$stmt= $conn->prepare($sql_p1);
$stmt->execute();
$result = $stmt->get_result();
$row_p1 = $result->fetch_assoc();
$pending_claims_p1 = $row_p1['COUNT(*)'];

$pending_claims_p2 = 0;
$sql_p2 = "SELECT COUNT(*) FROM Norm_Claims WHERE inserted=0 AND nonfactual=0 AND latest=1";
$stmt= $conn->prepare($sql_p2);
$stmt->execute();
$result = $stmt->get_result();
$row_p2 = $result->fetch_assoc();
$pending_claims_p2 = $row_p2['COUNT(*)'];

$pending_claims_p3 = 0;
$sql_p3 = "SELECT COUNT(*) FROM Assigned_Norms WHERE inserted=0 AND num_qapairs!=0";
$stmt= $conn->prepare($sql_p3);
$stmt->execute();
$result = $stmt->get_result();
$row_p3 = $result->fetch_assoc();
$pending_claims_p3 = $row_p3['COUNT(*)'];

$pending_claims_p4 = 0;
$sql_p4 = "SELECT COUNT(*) FROM Assigned_Valids WHERE inserted=0 AND phase_2_label != phase_3_label AND valid_latest=1";
$stmt= $conn->prepare($sql_p4);
$stmt->execute();
$result = $stmt->get_result();
$row_p4 = $result->fetch_assoc();
$pending_claims_p4 = $row_p4['COUNT(*)'];

$pending_claims_p5 = 0;
$sql_p5 = "SELECT COUNT(*) FROM Assigned_Disputes WHERE inserted=0 AND added_qas=1";
$stmt= $conn->prepare($sql_p5);
$stmt->execute();
$result = $stmt->get_result();
$row_p5 = $result->fetch_assoc();
$pending_claims_p5 = $row_p5['COUNT(*)'];

if ($result->num_rows > 0) {
    $p1_assigned_avg = round($row['SUM(p1_assigned)'] / max($active_users_p1, 1), 2);
    $p2_assigned_avg = round($row['SUM(p2_assigned)'] / max($active_users_p2, 1), 2);
    $p3_assigned_avg = round($row['SUM(p3_assigned)'] / max($active_users_p3, 1), 2);
    $p4_assigned_avg = round($row['SUM(p4_assigned)'] / max($active_users_p4, 1), 2);
    $p5_assigned_avg = round($row['SUM(p5_assigned)'] / max($active_users_p5, 1), 2);

    $p1_assigned = $row['SUM(p1_assigned)'];
    $p2_assigned = $row['SUM(p2_assigned)'];
    $p3_assigned = $row['SUM(p3_assigned)'];
    $p4_assigned = $row['SUM(p4_assigned)'];
    $p5_assigned = $row['SUM(p5_assigned)'];

    $p1_annotations_done = round($row['SUM(finished_norm_annotations)'] / max($active_users_p1, 1), 2);
    $p2_annotations_done = round($row['SUM(finished_qa_annotations)'] / max($active_users_p2, 1), 2);
    $p3_annotations_done = round($row['SUM(finished_valid_annotations)'] / max($active_users_p3, 1), 2);
    $p4_annotations_done = round($row['SUM(finished_dispute_annotations)'] / max($active_users_p4, 1), 2);
    $p5_annotations_done = round($row['SUM(finished_post_annotations)'] / max($active_users_p5, 1), 2);

    $p1_completed = $row['SUM(finished_norm_annotations)'];
    $p2_completed = $row['SUM(finished_qa_annotations)'];
    $p3_completed = $row['SUM(finished_valid_annotations)'];
    $p4_completed = $row['SUM(finished_dispute_annotations)'];
    $p5_completed = $row['SUM(finished_post_annotations)'];

    $p1_skipped_percent = round($row['SUM(skipped_norm_data)'] / max($active_users_p1, 1), 2);
    $p2_skipped_percent = round($row['SUM(skipped_qa_data)'] / max($active_users_p2, 1), 2);

    $p1_claims_skipped = $row['SUM(skipped_norm_data)'];
    $p2_claims_skipped = $row['SUM(skipped_qa_data)'];

    $p1_timed_out = round($row['SUM(p1_timed_out)'] / max($active_users_p1, 1), 2);
    $p2_timed_out = round($row['SUM(p2_timed_out)'] / max($active_users_p2, 1), 2);
    $p4_timed_out = round($row['SUM(p4_timed_out)'] / max($active_users_p4, 1), 2);

    $p1_speed_trap = round($row['SUM(p1_speed_trap)'] / max($active_users_p1, 1), 2);
    $p2_speed_trap = round($row['SUM(p2_speed_trap)'] / max($active_users_p2, 1), 2);
    $p3_speed_trap = round($row['SUM(p3_speed_trap)'] / max($active_users_p3, 1), 2);
    $p4_speed_trap = round($row['SUM(p4_speed_trap)'] / max($active_users_p4, 1), 2);
    $p5_speed_trap = round($row['SUM(p5_speed_trap)'] / max($active_users_p5, 1), 2);

    $p1_average_task_time = round($row['SUM(p1_time_sum)'] / max($row['SUM(finished_norm_annotations)'], 1), 2);
    $p2_average_task_time = round($row['SUM(p2_time_sum)'] / max($row['SUM(finished_qa_annotations)'], 1), 2);
    $p3_average_task_time = round($row['SUM(p3_time_sum)'] / max($row['SUM(finished_valid_annotations)'], 1), 2);
    $p4_average_task_time = round($row['SUM(p4_time_sum)'] / max($row['SUM(finished_dispute_annotations)'], 1), 2);
    $p5_average_task_time = round($row['SUM(p5_time_sum)'] / max($row['SUM(finished_post_annotations)'], 1), 2);

    $p1_average_load_time = round($row['SUM(p1_load_sum)'] / max($row['SUM(finished_norm_annotations)'], 1), 2);
    $p2_average_load_time = round($row['SUM(p2_load_sum)'] / max($row['SUM(finished_qa_annotations)'], 1), 2);
    $p4_average_load_time = round($row['SUM(p4_load_sum)'] / max($row['SUM(finished_dispute_annotations)'], 1), 2);

    $p2_average_questions = round($row['SUM(questions_p2)'] / max($row['SUM(finished_dispute_annotations)'], 1), 2);
    $p4_average_questions = round($row['SUM(questions_p4)'] / max($row['SUM(finished_post_annotations)'], 1), 2);

    $phase1 = (["skipped_claims_percentage" => $p1_skipped_percent, "pending_claims" => $pending_claims_p1, "assigned_claims" => $p1_assigned, "annotations_assigned" => $p1_assigned_avg,
    "speed_traps_hit" => $p1_speed_trap, "annotations_timed_out" => $p1_timed_out, "skipped_claims" => $p1_claims_skipped, "completed_claims" => $p1_completed, "annotations_done" => $p1_annotations_done,
    "average_load_time" => $p1_average_load_time, "average_task_time" => $p1_average_task_time]);

    $phase2 = (["skipped_claims_percentage" => $p2_skipped_percent, "average_questions_p2" => $p2_average_questions, "pending_claims" => $pending_claims_p2, "assigned_claims" => $p2_assigned, "annotations_assigned" => $p2_assigned_avg,
    "speed_traps_hit" => $p2_speed_trap, "annotations_timed_out" => $p2_timed_out, "skipped_claims" => $p2_claims_skipped, "completed_claims" => $p2_completed, "annotations_done" => $p2_annotations_done,
    "average_load_time" => $p2_average_load_time, "average_task_time" => $p2_average_task_time]);

    $phase3 = (["pending_claims" => $pending_claims_p3, "assigned_claims" => $p3_assigned, "annotations_assigned" => $p3_assigned_avg,
     "speed_traps_hit" => $p3_speed_trap, "completed_claims" => $p3_completed, "annotations_done" => $p3_annotations_done, "average_task_time" => $p3_average_task_time]);

    $phase4 = (["average_questions_p4" => $p4_average_questions, "pending_claims" => $pending_claims_p4, "assigned_claims" => $p4_assigned, "annotations_assigned" => $p4_assigned_avg,
    "speed_traps_hit" => $p4_speed_trap, "annotations_timed_out" => $p4_timed_out, "completed_claims" => $p4_completed, "annotations_done" => $p4_annotations_done,  "average_load_time" => $p4_average_load_time, "average_task_time" => $p4_average_task_time]);

    $phase5 = (["pending_claims" => $pending_claims_p5, "assigned_claims" => $p5_assigned, "annotations_assigned" => $p5_assigned_avg,
    "speed_traps_hit" => $p5_speed_trap, "completed_claims" => $p5_completed, "annotations_done" => $p5_annotations_done, "average_task_time" => $p5_average_task_time]);

    $output = (["phase_1" => $phase1, "phase_2" => $phase2, "phase_3" => $phase3, "phase_4" => $phase4, "phase_5" => $phase5]);
    echo(json_encode($output));

} else {
    echo "0 Results";
}

$conn->close();

?>