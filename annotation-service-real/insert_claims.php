<?php

$json_string = file_get_contents('all.json');
$data = json_decode($json_string, true);

$db_params = parse_ini_file(dirname(__FILE__).'/db_params.ini', false);

$conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


foreach($data as $item) {
    $claim_text = $item['claim_text'];
    // $source_claim = $item['source_claim'];
    // $source_claim_url = $item['source_claim_url'];
    // $verdict_article = $item['verdict_article'];
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


    // $fix_date = substr_replace($claim_date, '20', 6, 0);
    // echo $fix_date;

    $new_text=mysqli_real_escape_string($conn, $claim_text);
    // echo $new_text;
    // echo "<br>";

    // $sql = "INSERT INTO Claims (claim_text, source_claim, source_claim_url, verdict_article, web_archive, norm_annotators_num, taken_flag, skipped)
    // VALUES('$new_text', '$source_claim', '$source_claim_url', '$verdict_article', '$web_archive', 0, 0, 0)";

    $sql = "INSERT INTO Claims (claim_text, web_archive, claim_date, norm_annotators_num, norm_taken_flag, norm_skipped)
    VALUES('$new_text', '$web_archive', '$claim_date', 0, 0, 0)";

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
