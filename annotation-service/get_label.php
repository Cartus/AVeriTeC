<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Label";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "claim_id: " . $row["claim_id"]. "<br>";
        echo "claim_valid_id: " . $row["claim_valid_id"]. "<br>";
        echo "user_id_dispute: " . $row["user_id_dispute"]. "<br>";
        echo "phase_4_label: " . $row["phase_4_label"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>