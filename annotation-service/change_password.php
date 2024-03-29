<?php
date_default_timezone_set('UTC');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

function update_table($conn, $sql_command, string $types , ...$vars ) {
    $sql2 = $sql_command; // Add flag that current claim is taken. Need to be freed when evidence is submitted,
    $stmt= $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

if (empty($_POST['name']) && empty($_POST['password_md5'])) die();

$name = $_POST['name'];
$pw_md5 = $_POST['password_md5'];
$new_pw = $_POST['new_password'];
$new_pw_md5 = $_POST['new_password_md5'];

$sql = "SELECT user_id FROM Annotators WHERE password_md5=? AND user_name=?";
$stmt= $conn->prepare($sql);
$stmt->bind_param("ss", $pw_md5, $name);
$stmt->execute();

$result = $stmt->get_result();
$credentials_match = $result->num_rows > 0;

if ($credentials_match) {
    $row = $result->fetch_assoc();
    update_table($conn, "UPDATE Annotators SET password_md5=?, password_cleartext=? WHERE user_id=?", 'ssi', $new_pw_md5, $new_pw, $row['user_id']);
    echo(json_encode(["successful" => true, "message" => "Your password has been changed"]));
  }else{
    echo(json_encode(["successful" => false, "message" => "Something went wrong"]));
  }
 
$conn->close();
?>
