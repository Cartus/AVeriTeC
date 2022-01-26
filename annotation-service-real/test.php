<?php

$json_string = file_get_contents('all.json');
$data = json_decode($json_string, true);

// print_r($data);

$db_params = parse_ini_file(dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = "zhijiang";
$password = "admin";
$password_md5 = "admin";

$sql = "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, number_logins,
finished_norm_annotations, finished_qa_annotations, finished_valid_annotations) VALUES('$name', '$password', '$password_md5', 1, 0, 0, 0, 0)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("registered" => true));
} else {
    echo json_encode(["registered" => false, "message" => "Something went wrong"]);
}

$conn->close();

?>
