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

$sql = "SELECT SUM(p1_time_sum), SUM(p2_time_sum), SUM(p3_time_sum), SUM(p1_load_sum), SUM(p2_load_sum),
        SUM(finished_norm_annotations), SUM(finished_qa_annotations), SUM(finished_valid_annotations) FROM Annotators";
$stmt= $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows > 0) {
    $p1_average_task_time = round($row['SUM(p1_time_sum)'] / $row['SUM(finished_norm_annotations)'], 2);
    $p2_average_task_time = round($row['SUM(p2_time_sum)'] / $row['SUM(finished_qa_annotations)'], 2);
    $p3_average_task_time = round($row['SUM(p3_time_sum)'] / $row['SUM(finished_valid_annotations)'], 2);

    $p1_average_load_time = round($row['SUM(p1_load_sum)'] / $row['SUM(finished_norm_annotations)'], 2);
    $p2_average_load_time = round($row['SUM(p2_load_sum)'] / $row['SUM(finished_qa_annotations)'], 2);

    $phase1 = (["average_load_time" => $p1_average_load_time, "average_task_time" => $p1_average_task_time]);
    $phase2 = (["average_load_time" => $p2_average_load_time, "average_task_time" => $p2_average_task_time]);
    $phase3 = (["average_task_time" => $p3_average_task_time]);
    $output = (["phase1" => $phase1, "phase2" => $phase2, "phase3" => $phase3]);
    echo(json_encode($output));
} else {
    echo "0 Results";
}

$conn->close();

?>