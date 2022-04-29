<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Assigned_Disputes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        echo "claim_id: " . $row["claim_id"]. "<br>";
        echo "claim_qa_id: " . $row["claim_qa_id"]. "<br>";
        echo "claim_valid_id: " . $row["claim_valid_id"]. "<br>";
        echo "web_archive: " . $row["web_archive"]. "<br>";
        echo "user_id_norm: " . $row["user_id_norm"]. "<br>";
        echo "user_id_qa: " . $row["user_id_qa"]. "<br>";
        echo "user_id_valid: " . $row["user_id_valid"]. "<br>";
        echo "user_id_dispute: " . $row["user_id_dispute"]. "<br>";
        echo "qa_annotators_num: " . $row["qa_annotators_num"]. "<br>";
        echo "valid_annotators_num: " . $row["valid_annotators_num"]. "<br>";
        echo "dispute_annotators_num: " . $row["dispute_annotators_num"]. "<br>";
        echo "cleaned_claim: " . $row["cleaned_claim"]. "<br>";
        echo "correction_claim: " . $row["correction_claim"]. "<br>";
        echo "speaker: " . $row["speaker"]. "<br>";
        echo "hyperlink: " . $row["hyperlink"]. "<br>";
        echo "source: " . $row["source"]. "<br>";
        echo "transcription: " . $row["transcription"]. "<br>";
        echo "media_source: " . $row["media_source"]. "<br>";
        echo "check_date: " . $row["check_date"]. "<br>";
        echo "claim_loc: " . $row["claim_loc"]. "<br>";
        echo "claim_types: " . $row["claim_types"]. "<br>";
        echo "fact_checker_strategy: " . $row["fact_checker_strategy"]. "<br>";
        echo "phase_1_label: " . $row["phase_1_label"]. "<br>";
        echo "phase_2_label: " . $row["phase_2_label"]. "<br>";
        echo "phase_3_label: " . $row["phase_3_label"]. "<br>";
        echo "phase_4_label: " . $row["phase_4_label"]. "<br>";
	    echo "justification: " . $row["justification"]. "<br>";
	    echo "unreadable: " . $row["unreadable"]. "<br>";
        echo "qa_skipped: " . $row["qa_skipped"]. "<br>";
        echo "qa_skipped_by: " . $row["qa_skipped_by"]. "<br>";
        echo "num_qapairs: " . $row["num_qapairs"]. "<br>";
        echo "latest: " . $row["latest"]. "<br>";
        echo "valid_latest: " . $row["valid_latest"]. "<br>";
        echo "dispute_latest: " . $row["dispute_latest"]. "<br>";
        echo "date_start_norm: " . $row["date_start_norm"]. "<br>";
        echo "date_load_norm: " . $row["date_load_norm"]. "<br>";
        echo "date_made_norm: " . $row["date_made_norm"]. "<br>";
        echo "date_restart_norm: " . $row["date_restart_norm"]. "<br>";
        echo "date_modified_norm: " . $row["date_modified_norm"]. "<br>";
        echo "date_start_qa: " . $row["date_start_qa"]. "<br>";
        echo "date_load_qa: " . $row["date_load_qa"]. "<br>";
        echo "date_made_qa: " . $row["date_made_qa"]. "<br>";
        echo "date_restart_qa: " . $row["date_restart_qa"]. "<br>";
        echo "date_modified_qa: " . $row["date_modified_qa"]. "<br>";
        echo "date_start_valid: " . $row["date_start_valid"]. "<br>";
        echo "date_made_valid: " . $row["date_made_valid"]. "<br>";
        echo "date_restart_valid: " . $row["date_restart_valid"]. "<br>";
        echo "date_modified_valid: " . $row["date_modified_valid"]. "<br>";
        echo "date_start_dispute: " . $row["date_start_dispute"]. "<br>";
        echo "date_load_dispute: " . $row["date_load_dispute"]. "<br>";
        echo "date_load_cache_dispute: " . $row["date_load_cache_dispute"]. "<br>";
        echo "date_made_dispute: " . $row["date_made_dispute"]. "<br>";
        echo "date_restart_dispute: " . $row["date_restart_dispute"]. "<br>";
        echo "date_modified_dispute: " . $row["date_modified_dispute"]. "<br>";
        echo "inserted: " . $row["inserted"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>