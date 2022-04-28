<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$name = $_POST['name'];
$password = $_POST['password'];
$password_md5 = $_POST['password_md5'];

if (empty($_POST['name']) || empty($_POST['password'])) die();

$query = "SELECT * FROM Annotators";
$result = mysqli_query($conn, $query);

// see if any rows were returned
if (mysqli_num_rows($result) > 0) {
    $sql = "SELECT * FROM Annotators WHERE user_name = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if(mysqli_num_rows($result) > 0) {
        echo json_encode(["duplicated" => true]);
    } else {
        $sql = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, is_active, number_logins,
                finished_norm_annotations, finished_qa_annotations, finished_valid_annotations, finished_res_annotations, finished_post_annotations, 
                skipped_norm_data, skipped_qa_data,
                p1_time_sum, p1_load_sum, p2_time_sum, p2_load_sum, p3_time_sum, p4_time_sum, p4_load_sum, p5_time_sum,
                p1_timed_out, p2_timed_out, p4_timed_out,
                p1_speed_trap, p2_speed_trap, p3_speed_trap, p4_speed_trap, p5_speed_trap, 
                p1_assigned, p2_assigned, p3_assigned, p4_assigned, p5_assigned, 
                questions_p2, questions_p4) 
                VALUES('$name', '$password', '$password_md5', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";


        if ($conn->query($sql) === TRUE) {
            echo json_encode(["duplicated" => false, "registered" => true]);
        } else {
            echo json_encode(["duplicated" => false, "registered" => false, "message" => "Something went wrong"]);
        }
    }
}
else {
    $sql2 = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, is_active, number_logins,
            finished_norm_annotations, finished_qa_annotations, finished_valid_annotations, finished_res_annotations, finished_post_annotations, 
            skipped_norm_data, skipped_qa_data,
            p1_time_sum, p1_load_sum, p2_time_sum, p2_load_sum, p3_time_sum, p4_time_sum, p4_load_sum, p5_time_sum,
            p1_timed_out, p2_timed_out, p4_timed_out,
            p1_speed_trap, p2_speed_trap, p3_speed_trap, p4_speed_trap, p5_speed_trap, 
            p1_assigned, p2_assigned, p3_assigned, p4_assigned, p5_assigned, 
            questions_p2, questions_p4) 
            VALUES('$name', '$password', '$password_md5', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";


    if ($conn->query($sql2) === TRUE) {
        echo json_encode(["duplicated" => false, "registered" => true]);
    } else {
        echo json_encode(["duplicated" => false, "registered" => false, "message" => "Something went wrong"]);
    }
}


$conn->close();
?>