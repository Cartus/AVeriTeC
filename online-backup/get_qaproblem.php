<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Qaproblem";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "problem_id: " . $row["problem_id"]. "<br>";
        echo "qa_id: " . $row["qa_id"]. "<br>";
        echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        echo "user_id_qa: " . $row["user_id_qa"]. "<br>";
        echo "answer_problems: " . $row["answer_problems"]. "<br>";
        echo "answer_problems_second: " . $row["answer_problems_second"]. "<br>";
        echo "answer_problems_third: " . $row["answer_problems_third"]. "<br>";
        echo "question_problems: " . $row["question_problems"]. "<br>";
        echo "<br>";
    }
} else {
    echo "0 Results";
}

$conn->close();
?>
