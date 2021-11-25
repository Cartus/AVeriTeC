<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Search_Record";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "search_id: " . $row["search_id"]. "<br>";
        echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        echo "user_id_qa: " . $row["user_id_qa"]. "<br>";
        echo "query: " . $row["query"]. "<br>";
        echo "abstract: " . $row["abstract"]. "<br>";
        echo "header: " . $row["header"]. "<br>";
        echo "problematic: " . $row["problematic"]. "<br>";
        echo "result_url: " . $row["result_url"]. "<br>";
        echo "date_query: " . $row["date_query"]. "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>
