<?php

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt = $conn->prepare($sql2);
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


$id = 30;
// $claim_types = 'Causal Claim [SEP] Event/Property Claim';

update_table($conn, "UPDATE Train_Norm_Claims SET cleaned_claim='After legalizing marijuana in Colorado, there has been a spike in consumption.' WHERE claim_norm_id=?", 'i', $id);

// update_table($conn, "UPDATE Train_Norm_Claims SET speaker='John Hickenlooper' AND hyperlink='https://web.archive.org/web/20210127201932/http://www.cnn.com/TRANSCRIPTS/1903/20/se.01.html'
// -- AND source='CNN' AND check_date='2019-03-20' AND claim_types='Causal Claim [SEP] Numerical Claim' AND
// -- fact_checker_strategy='Written Evidence' WHERE claim_norm_id=?", 'i', $id);

$conn->close();
?>

