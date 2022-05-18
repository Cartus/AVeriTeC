<?php

$json_string = file_get_contents('train.json');
$data = json_decode($json_string, true);

$db_params = parse_ini_file(dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


foreach($data as $item) {
    $claim_text = $item['claim_text'];
    $web_archive = $item['web_archive'];

    if (array_key_exists('fact_checking_date', $item)){
        $raw_date = substr($item['fact_checking_date'], 0, 8);
        $year = '20' . substr($raw_date, 6, 8);
        $month = substr($raw_date, 2, 4);
        $day = substr($raw_date, 0, 2);
        $claim_date = $year . $month . $day;
    } else {
        $claim_date = NULL;
    }

    echo $claim_date;

    $new_text=mysqli_real_escape_string($conn, $claim_text);

    $sql = "INSERT INTO Train_Claims (claim_text, web_archive, claim_date)
    VALUES('$new_text', '$web_archive', '$claim_date')";

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
