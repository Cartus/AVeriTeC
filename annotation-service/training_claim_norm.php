<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt = $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$date = date("Y-m-d H:i:s");

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$req_type = $_POST['req_type'];

if ($req_type == "load-data"){

    $offset = $_POST['offset'];
    
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // TODO: gold user id
    $user_id = 1;

    $sql = "SELECT claim_id FROM Claim_Map WHERE user_id=? ORDER BY date_made DESC LIMIT 1 OFFSET ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $latest = 1;
    $sql_norm = "SELECT * FROM Train_Norm_Claims WHERE latest=? AND claim_id=? AND user_id_norm=?";
    $stmt = $conn->prepare($sql_norm);
    $stmt->bind_param("iii", $latest, $row['claim_id'], $user_id);
    $stmt->execute();
    $result_norm = $stmt->get_result();

    $entries = array();
    $counter = 0;
    if ($result_norm->num_rows > 0) {
        while($row_norm = $result_norm->fetch_assoc()) {
            $count_string = "claim_entry_field_" . (string)$counter;
            $counter = $counter + 1;
            $norm_array = array();
            $norm_array['fact_checker_strategy'] = explode(" [SEP] ", $row_norm['fact_checker_strategy']);
            $norm_array['claim_types'] = explode(" [SEP] ", $row_norm['claim_types']);
            $norm_array['cleaned_claim'] = $row_norm['cleaned_claim'];
            $norm_array['phase_1_label'] = $row_norm['phase_1_label'];
            $norm_array['speaker'] = $row_norm['speaker'];
            $norm_array['hyperlink'] = $row_norm['hyperlink'];
            $norm_array['source'] = $row_norm['source'];
            $norm_array['transcription'] = $row_norm['transcription'];
            $norm_array['media_source'] = $row_norm['media_source'];
            $norm_array['date'] = $row_norm['check_date'];
            $norm_array['location'] = $row_norm['claim_loc'];
            $entries[$count_string] = $norm_array;
            $web_archive = $row_norm['web_archive'];
        }

        $output = (["claim_id" => $row['claim_id'], "web_archive" => $web_archive, "entries" => $entries]);
        echo(json_encode($output));
    } else {
        echo "0 Results";
    }
    $conn->close();

} 

?>