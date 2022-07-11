<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Norm_Claims";
$result = $conn->query($sql);

$gold_array = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        // echo "claim_id: " . $row["claim_id"]. "<br>";
        // echo "web_archive: " . $row["web_archive"]. "<br>";
        // echo "user_id_norm: " . $row["user_id_norm"]. "<br>";
        // echo "cleaned_claim: " . $row["cleaned_claim"]. "<br>";
        // echo "correction_claim: " . $row["correction_claim"]. "<br>";
        // echo "speaker: " . $row["speaker"]. "<br>";
        // echo "hyperlink: " . $row["hyperlink"]. "<br>";
        // echo "source: " . $row["source"]. "<br>";
        // echo "transcription: " . $row["transcription"]. "<br>";
        // echo "media_source: " . $row["media_source"]. "<br>";
        // echo "check_date: " . $row["check_date"]. "<br>";
        // echo "claim_loc: " . $row["claim_loc"]. "<br>";
        // echo "claim_types: " . $row["claim_types"]. "<br>";
        // echo "fact_checker_strategy: " . $row["fact_checker_strategy"]. "<br>";
        // echo "phase_1_label: " . $row["phase_1_label"]. "<br>";
        // echo "latest: " . $row["latest"]. "<br>";
        // echo "date_start_norm: " . $row["date_start_norm"]. "<br>";
        // echo "date_load_norm: " . $row["date_load_norm"]. "<br>";
        // echo "date_made_norm: " . $row["date_made_norm"]. "<br>";
        // echo "date_restart_norm: " . $row["date_restart_norm"]. "<br>";
        // echo "date_modified_norm: " . $row["date_modified_norm"]. "<br>";
        // echo "nonfactual: " . $row["nonfactual"]. "<br>";
        // echo "inserted: " . $row["inserted"]. "<br>";
        // echo "<br>";

        array_push($gold_array, ["claim_norm_id"=> $row['claim_norm_id'], "claim_id"=> $row['claim_id'], "web_archive"=> $row['web_archive'], "user_id_norm"=> $row['user_id_norm'],
        "cleaned_claim"=> $row['cleaned_claim'], "correction_claim"=> $row['correction_claim'], "speaker"=> $row['speaker'], "hyperlink"=> $row['hyperlink'], "source"=> $row['source'],
        "transcription"=> $row['transcription'], "media_source"=> $row['media_source'], "check_date"=> $row['check_date'], "claim_loc"=> $row['claim_loc'], "claim_types"=> $row['claim_types'],
        "fact_checker_strategy"=> $row['fact_checker_strategy'], "phase_1_label"=> $row['phase_1_label'], "latest"=> $row['latest'], "date_start_norm"=> $row['date_start_norm'], ""=> $row[''],
        "date_load_norm"=> $row['date_load_norm'], "date_made_norm"=> $row['date_made_norm'], "date_restart_norm"=> $row['date_restart_norm'], "date_modified_norm"=> $row['date_modified_norm'],
        "nonfactual"=> $row['nonfactual'], "inserted"=> $row['inserted']])
    }
} else {
    echo "0 Results";
}

$json = json_encode($gold_array);

//write json to file
if (file_put_contents("results/p1_norms.json", $json))
    echo "JSON file created successfully...";
else
    echo "Oops! Error creating json file...";

$conn->close();
?>
