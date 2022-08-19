<?php
date_default_timezone_set('UTC');
$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Qapair";
$result = $conn->query($sql);

$gold_array = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // echo "qa_id: " . $row["qa_id"]. "<br>";
        // echo "claim_norm_id: " . $row["claim_norm_id"]. "<br>";
        // echo "user_id_qa: " . $row["user_id_qa"]. "<br>";
        // echo "question: " . $row["question"]. "<br>";
        // echo "answer: " . $row["answer"]. "<br>";
        // echo "source_url: " . $row["source_url"]. "<br>";
        // echo "answer_type: " . $row["answer_type"]. "<br>";
        // echo "source_medium: " . $row["source_medium"]. "<br>";
        // echo "answer_problems: " . $row["answer_problems"]. "<br>";
        // echo "bool_explanation: " . $row["bool_explanation"]. "<br>";
        // echo "answer_second: " . $row["answer_second"]. "<br>";
        // echo "source_url_second: " . $row["source_url_second"]. "<br>";
        // echo "answer_type_second: " . $row["answer_type_second"]. "<br>";
        // echo "source_medium_second: " . $row["source_medium_second"]. "<br>";
        // echo "answer_problems_second: " . $row["answer_problems_second"]. "<br>";
        // echo "bool_explanation_second: " . $row["bool_explanation_second"]. "<br>";
        // echo "answer_third: " . $row["answer_third"]. "<br>";
        // echo "source_url_third: " . $row["source_url_third"]. "<br>";
        // echo "answer_type_third: " . $row["answer_type_third"]. "<br>";
        // echo "source_medium_third: " . $row["source_medium_third"]. "<br>";
        // echo "answer_problems_third: " . $row["answer_problems_third"]. "<br>";
        // echo "bool_explanation_third: " . $row["bool_explanation_third"]. "<br>";
        // echo "question_problems: " . $row["question_problems"]. "<br>";
        // echo "qa_latest: " . $row["qa_latest"]. "<br>";
        // echo "edit_latest: " . $row["edit_latest"]. "<br>";
        // echo "p4_latest: " . $row["p4_latest"]. "<br>";
        // echo "date_start: " . $row["date_start"]. "<br>";
        // echo "date_load: " . $row["date_load"]. "<br>";
        // echo "date_made: " . $row["date_made"]. "<br>";
        // echo "<br>";

        array_push($gold_array, ["qa_id"=>$row['qa_id'], "claim_norm_id"=>$row['claim_norm_id'], "user_id_qa"=>$row['user_id_qa'], "question"=>$row['question'], "answer"=>$row['answer'], "source_url"=>$row['source_url'],
        "source_url"=>$row['source_url'], "answer_type"=>$row['answer_type'], "source_medium"=>$row['source_medium'], "answer_problems"=>$row['answer_problems'], "bool_explanation"=>$row['bool_explanation'],
        "answer_second"=>$row['answer_second'], "source_url_second"=>$row['source_url_second'], "answer_type_second"=>$row['answer_type_second'], "source_medium_second"=>$row['source_medium_second'],
        "answer_problems_second"=>$row['answer_problems_second'], "bool_explanation_second"=>$row['bool_explanation_second'], "answer_third"=>$row['answer_third'], "source_url_third"=>$row['source_url_third'],
        "answer_type_third"=>$row['answer_type_third'], "source_medium_third"=>$row['source_medium_third'], "answer_problems_third"=>$row['answer_problems_third'], "bool_explanation_third"=>$row['bool_explanation_third'],
        "question_problems"=>$row['question_problems'], "qa_latest"=>$row['qa_latest'], "edit_latest"=>$row['edit_latest'], "p4_latest"=>$row['p4_latest'],
        "date_start"=>$row['date_start'], "date_load"=>$row['date_load'], "date_made"=>$row['date_made']]);
    }
} else {
    echo "0 Results";
}

$json = json_encode($gold_array);

//write json to file
if (file_put_contents("results/p2_qas.json", $json))
    echo "JSON file created successfully...";
else
    echo "Oops! Error creating json file...";

$conn->close();
?>