<?php

//$_SESSION = array();

$name = $_GET["name"];
$pw = $_GET["pw"];
$pw_md5 = $_GET["pw_md5"];
$mode = $_GET["mode"];

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$servername = "localhost";
$username = $db_params['user'];
$password = $db_params['password'];
$dbname = $db_params['database'];

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO Annotators (annotator_name, password_cleartext, password_md5, annotation_mode) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $pw, $pw_md5, $mode);
$stmt->execute();



$conn->close();



?>