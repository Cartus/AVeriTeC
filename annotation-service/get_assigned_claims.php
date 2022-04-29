<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Assigned_Claims";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "claim_id: " . $row["claim_id"]. "<br>";
        echo "raw_id: " . $row["raw_id"]. "<br>";
        echo "claim_text: " . $row["claim_text"]. "<br>";
        echo "web_archive: " . $row["web_archive"]. "<br>";
        echo "claim_date: " . $row["claim_date"]. "<br>";
        echo "norm_annotators_num: " . $row["norm_annotators_num"]. "<br>";
        echo "user_id_norm : " . $row["user_id_norm"]. "<br>";
        echo "norm_skipped: " . $row["norm_skipped"]. "<br>";
        echo "norm_skipped_by: " . $row["norm_skipped_by"]. "<br>";
        echo "date_start_norm : " . $row["date_start_norm"]. "<br>";
        echo "date_skip_norm : " . $row["date_skip_norm"]. "<br>";
        echo "date_load_norm : " . $row["date_load_norm"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>