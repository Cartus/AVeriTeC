<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Qapair";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "qa_id: " . $row["qa_id"]. "<br>";
        echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        echo "user_id_qa: " . $row["user_id_qa"]. "<br>";
        echo "question: " . $row["question"]. "<br>";
        echo "answer: " . $row["answer"]. "<br>";
        echo "source_url: " . $row["source_url"]. "<br>";
        echo "answer_type: " . $row["answer_type"]. "<br>";
        echo "source_medium: " . $row["source_medium"]. "<br>";
        echo "question_problems: " . $row["question_problems"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>