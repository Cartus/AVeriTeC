<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json: charset=utf-8');


$pythonpath = "/home/michael/anaconda3/envs/averitec/bin/python";
$scriptpath = 'averitec_web_search.py';

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$user_id = $_POST['user_id'];
$claim_norm_id = $_POST['claim_norm_id'];

$query = $_POST['query'];
$page = $_POST['page'];
$claim_date = $_POST['claim_date'];
$country_code = $_POST['country_code'];

$command = "{$pythonpath} {$scriptpath} --query \"{$query}\" --page {$page} --claim_date {$claim_date} --country_code {$country_code} 2>&1";

exec($command, $output);
//print_r($output);
//echo $output[0];

if ($output[0] == "No") {
    exit("");
}

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt =  $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}


$date = date("Y-m-d H:i:s");
$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
}

$result = array();
$conn->begin_transaction();
try {
    foreach($output as $item) {
	$sub = array();    
	$split = explode("<", $item);
	//print_r($split);

	$url = $split[0];
        $header = $split[1];
	$abstract = $split[2];
	$problematic = $split[3];

        update_table($conn, "INSERT INTO Search_Record (claim_norm_id, user_id_qa, query, abstract, header, 
        problematic, result_url, country_code, date_query) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssss', 
	$claim_norm_id, $user_id, $query, $abstract, $header, $problematic, $url, $country_code, $date);

        if($problematic == 'False'){
            $problematic = NULL;
	} 

        $sub = (["url" => $url, "header" => $header, "abstract" => $abstract, "problematic" => $problematic]);
	array_push($result, $sub);
	
	$conn->commit();
    }
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    throw $exception;
}

echo(json_encode($result));
$conn->close();
?>

