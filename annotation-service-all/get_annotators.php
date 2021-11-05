<?php

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);

// Create connection
$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Annotators";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["user_id"]. "<br>"; 
        echo "Name: " . $row["user_name"]. "<br>"; 
        echo "Admin: " . $row["is_admin"]. "<br>"; 
        echo "Password: " . $row["password_cleartext"]. "<br>"; 
        echo "current_norm_task: " . $row["current_norm_task"]. "<br>"; 
        echo "current_qa_task: " . $row["current_qa_task"]. "<br>"; 
        echo "current_valid_task: " . $row["current_valid_task"]. "<br>";
        echo "finished_norm_annotations: " . $row["finished_norm_annotations"]. "<br>"; 
        echo "finished_qa_annotations: " . $row["finished_qa_annotations"]. "<br>";  
        echo "finished_valid_annotations: " . $row["finished_valid_annotations"]. "<br>"; 
        echo "Phase: " . $row["annotation_phase"]. "<br>"; 
        echo "number_logins: " . $row["number_logins"]. "<br>"; 
        echo "<br>"; 
    }
} else {
    echo "0 Results";
}

$conn->close();
?>
