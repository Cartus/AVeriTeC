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

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        echo "claim_id: " . $row["claim_id"]. "<br>";
        echo "web_archive: " . $row["web_archive"]. "<br>";
        echo "user_id_norm: " . $row["user_id_norm"]. "<br>";
        echo "user_id_qa: " . $row["user_id_qa"]. "<br>";
        echo "user_id_valid: " . $row["user_id_valid"]. "<br>";
        echo "qa_annotators_num: " . $row["qa_annotators_num"]. "<br>";
        echo "valid_annotators_num: " . $row["valid_annotators_num"]. "<br>";
        echo "cleaned_claim: " . $row["cleaned_claim"]. "<br>";
        echo "speaker: " . $row["speaker"]. "<br>";
        echo "hyperlink: " . $row["hyperlink"]. "<br>";
        echo "transcription: " . $row["transcription"]. "<br>";
        echo "media_source: " . $row["media_source"]. "<br>";
        echo "check_date: " . $row["check_date"]. "<br>";
        echo "claim_loc: " . $row["claim_loc"]. "<br>";
        echo "claim_types: " . $row["claim_types"]. "<br>";
        echo "fact_checker_strategy: " . $row["fact_checker_strategy"]. "<br>";
        echo "phase_1_label: " . $row["phase_1_label"]. "<br>";
        echo "phase_2_label: " . $row["phase_2_label"]. "<br>";
        echo "phase_3_label: " . $row["phase_3_label"]. "<br>";
	echo "justification: " . $row["justification"]. "<br>";
	echo "bias: " . $row["bias"]. "<br>";
        echo "qa_taken_flag: " . $row["qa_taken_flag"]. "<br>";
        echo "qa_skipped: " . $row["qa_skipped"]. "<br>";
        echo "valid_taken_flag: " . $row["valid_taken_flag"]. "<br>";
        echo "has_qapairs: " . $row["has_qapairs"]. "<br>";
        echo "date_made_norm: " . $row["date_made_norm"]. "<br>";
        echo "date_made_qa: " . $row["date_made_qa"]. "<br>";
        echo "date_made_valid: " . $row["date_made_valid"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>
