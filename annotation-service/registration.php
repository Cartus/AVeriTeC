<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
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
        $sql = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, number_logins,
        finished_norm_annotations, finished_qa_annotations, finished_valid_annotations, skipped_norm_data, skipped_qa_data) VALUES('$name', '$password', '$password_md5', 0, 0, 0, 0, 0, 0, 0)";

        // $sql = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, number_logins,
        // finished_norm_annotations, finished_qa_annotations, finished_valid_annotations) VALUES('$name', '$password', '$password_md5', 0, 0, 0, 0, 0)";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["duplicated" => false, "registered" => true]);
        } else {
            echo json_encode(["duplicated" => false, "registered" => false, "message" => "Something went wrong"]);
        }
    }
}
else {
    $sql2 = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, number_logins,
    finished_norm_annotations, finished_qa_annotations, finished_valid_annotations, skipped_norm_data, skipped_qa_data) VALUES('$name', '$password', '$password_md5', 0, 0, 0, 0, 0, 0, 0)";

    // $sql2 = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, number_logins,
    // finished_norm_annotations, finished_qa_annotations, finished_valid_annotations) VALUES('$name', '$password', '$password_md5', 0, 0, 0, 0, 0)";

    if ($conn->query($sql2) === TRUE) {
        echo json_encode(["duplicated" => false, "registered" => true]);
    } else {
        echo json_encode(["duplicated" => false, "registered" => false, "message" => "Something went wrong"]);
    }
}


$conn->close();
?>
