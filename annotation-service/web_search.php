<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

// Leaving this because we may want to do session validation before search (?)

// $servername = "localhost";
// $username = "root";
// $password = "1qaz2wsx";
// $dbname = "MyDB";

//$conn = new mysqli($servername, $username, $password, $dbname);

//if ($conn->connect_error) {
//    die("Connection Failed: " . $conn->connect_error);
//}

$pythonpath = "/home/michael/anaconda3/envs/averitec/bin/python";
$scriptpath = 'averitec_web_search.py';

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$query = $_GET['query'];
$page = $_GET['page'];
$claim_date = $_GET['claim_date'];

$command = "{$pythonpath} {$scriptpath} --query \"{$query}\" --page {$page} --claim_date {$claim_date} 2>&1";

$output = shell_exec($command);

echo $output;


// $conn->close();
?>
