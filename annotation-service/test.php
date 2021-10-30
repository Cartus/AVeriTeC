<?php

$json_string = file_get_contents('all.json');
$data = json_decode($json_string, true);

// print_r($data);

$db_params = parse_ini_file(dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$claim_id = 100;
$web_archive = 'sss';
$taken_flag = 0;
$skipped = 1;

$stmt = $conn->prepare("INSERT INTO Norm_Claims (claim_id, web_archive, taken_flag, skipped) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isii", $claim_id, $web_archive, $taken_flag, $skipped);
 
// 设置参数并执行
$stmt->execute();
 
echo "新记录插入成功";
 
$stmt->close();
$conn->close();

// $sql = "INSERT INTO Norm_Claims (claim_id, web_archive, taken_flag, skipped) 
//     VALUES(1, 'sss', 0, 0)";

// if ($conn->query($sql) === TRUE) {
//     echo "Inserted successfully";
//     echo "<br>";
// } else {
//     echo "Error creating table: " . $conn->error;
//     echo "<br>";
// }


?>