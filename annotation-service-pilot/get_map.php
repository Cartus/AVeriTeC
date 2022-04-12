<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Claim_Map";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "user_id: " . $row["user_id"]. "<br>";
        echo "claim_id: " . $row["claim_id"]. "<br>";
        echo "skipped: " . $row["skipped"]. "<br>";
        echo "date_made: " . $row["date_made"]. "<br>";
        echo "date_start: " . $row["date_start"]. "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>