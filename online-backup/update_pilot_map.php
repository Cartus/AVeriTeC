<?php

$db_params = parse_ini_file(dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$date = date("Y-m-d H:i:s");
$skip = 0;
$user_id = 2;

for ($x = 1; $x <= 20; $x++) {
    $sql = "INSERT INTO Claim_Map (user_id, claim_id, skipped, date_made) 
     VALUES('$user_id', '$x', '$skip', '$date')";

    if ($conn->query($sql) === TRUE) {
        echo "Inserted successfully";
        echo "<br>";
    } else {
        echo "Error creating table: " . $conn->error;
        echo "<br>";
    }
    
}

$conn->close();

?>

