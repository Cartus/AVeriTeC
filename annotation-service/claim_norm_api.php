<?php


function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt =  $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$date = date("Y-m-d H:i:s");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req = $_POST["request"];
}else if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $req = $_GET["request"];
}else{
    $req = $argv[1];
}

$user_id = $_SESSION['user'];

$claim_table = "Claims";
$qa_table = "Qapairs";

if ($_SESSION['annotation_mode'] != 'claim_normalization') { #Checks for existing session
    echo -2;
    return;
}

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);
$annotations_params = parse_ini_file( dirname(__FILE__).'/annotation_params.ini', false);

if ($req == "skip-annotation"){
    $servername = "localhost";
    $username = $db_params['user'];
    $password = $db_params['password'];
    $dbname = $db_params['database'];
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT annotator_id, annotation_phase, current_task FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_task'] != 0) {
            $sql = "SELECT claim_id FROM Claims WHERE claim_id = ?";
            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); //get first result

            $conn->begin_transaction();
            try {
                update_table($conn, "UPDATE Claims SET taken_flag=0, skipped=skipped+1, skipped_by=? WHERE claim_id=?", 'ii', $user_id, $row['claim_id']);
                update_table($conn, "UPDATE Annotators SET current_task=0, skipped_data=skipped_data+1 WHERE annotator_id=?", 'i', $user_id);
                $conn->commit();
            }catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                throw $exception;
            }
        }
    }
    $conn->close();

}else if ($req == "next-data"){

    $servername = "localhost";
    $username = $db_params['user'];
    $password = $db_params['password'];
    $dbname = $db_params['database'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT user_id, annotation_phase, current_task FROM Annotators WHERE user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['current_task'] != 0) {

            $sql = "SELECT claim_id, source_claim, source_claim_url, verdict_article, url_article FROM Claims WHERE claim_id = ?";

            $stmt= $conn->prepare($sql);
            $stmt->bind_param("i", $row['current_task']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); //get first result
            $output =  array($row['claim_id'], $row['source_claim'], $row['source_claim_url'], $row['verdict_article'], $row['url_article']);
            echo json_encode($output);
        } else {
            $sql = "SELECT claim_id, source_claim, source_claim_url, verdict_article, url_article FROM $claim_table WHERE norm_annotators_num = 0 AND taken_flag=0 AND skipped=0  ORDER BY RAND() LIMIT 1 ";
            $result = $conn->query($sql);
            $err = "Error description select data 4: " . $conn -> error;
            $row = $result->fetch_assoc(); // get first result
            $output =  array($row['claim_id'], $row['source_claim'], $row['source_claim_url'], $row['verdict_article'], $row['url_article']);

            echo json_encode($output);
            $conn->begin_transaction();
            try {
                if(!is_null($row['id'])){
                    update_table($conn, "UPDATE Claims SET taken_flag=1 WHERE claim_id=?", 'i', $row['claim_id']);
                    update_table($conn, "UPDATE Annotators SET current_task=? WHERE user_id=?", 'ii', $row['claim_id'], $user_id);
                }
                $conn->commit();
            }catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                throw $exception;
            }
        }
    }
    $conn->close();

} else if ($req == "claim-submission") {

    $servername = "localhost";
    $username = $db_params['user'];
    $password = $db_params['password'];
    $dbname = $db_params['database'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_POST["user_id"];
    $claim_id = $_POST["claim _id"];

    $url_previous_checker = $_POST["url_previous_checker"];
    $transcription = $_POST["transcription"];

    $norm_claim1 = $_POST["norm_claim1"];
    $type_claim1 = $_POST["type_claim1"];
    $strategy_claim1 = $_POST["strategy_claim1"];
    $verdict_claim1 = $_POST["verdict_claim1"];

    $norm_claim2 = $_POST["norm_claim2"];
    $type_claim2 = $_POST["type_claim2"];
    $strategy_claim2 = $_POST["strategy_claim2"];
    $verdict_claim2 = $_POST["verdict_claim2"];

    $norm_claim3 = $_POST["norm_claim3"];
    $type_claim3 = $_POST["type_claim3"];
    $strategy_claim3 = $_POST["strategy_claim3"];
    $verdict_claim3 = $_POST["verdict_claim3"];

    $norm_claim4 = $_POST["norm_claim4"];
    $type_claim4 = $_POST["type_claim4"];
    $strategy_claim4 = $_POST["strategy_claim4"];
    $verdict_claim4 = $_POST["verdict_claim4"];

    $norm_claim5 = $_POST["norm_claim5"];
    $type_claim5 = $_POST["type_claim5"];
    $strategy_claim5 = $_POST["strategy_claim5"];
    $verdict_claim5 = $_POST["verdict_claim5"];

    $date_claim = $_POST["date_claim"];
    $times = $_POST['times'];
    $total_time = $_POST['total_time'];

    $sql = "SELECT claim_id, url_article, verdict_article FROM $claim_table WHERE (id = (SELECT current_task FROM Annotators WHERE user_id=?))";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        if ($row['claim_id'] != $claim_id){
            echo ('Claim ID and Annotator task do not match. ');
        }

        $conn->begin_transaction();
        try {
            update_table($conn, "UPDATE $claim_table SET user_i=?, url_previous_checker=?,transcription=?, norm_claim1=?, type_claim1=?, strategy_claim1=?,verdict_claim1=?,norm_claim2=?, type_claim2=?, strategy_claim2=?, verdict_claim2=?, norm_claim3=?, type_claim3=?, strategy_claim3=?, verdict_claim3=?, norm_claim4=?, type_claim4=?, strategy_claim4=?,verdict_claim4=?, norm_claim5=?, type_claim5=?, strategy_claim5=?, verdict_claim5=?, 
                 total_annotation_time=?, annotation_time_events=?, date_made=?, taken_flag=0, norm_annotators_num = norm_annotators_num+1 WHERE claim_id=?", 'isssssssssssssssssssssssssi',
                $user_id, $url_previous_checker, $transcription, $norm_claim1, $type_claim1, $strategy_claim1, $verdict_claim1, $norm_claim2, $type_claim2, $strategy_claim2, $verdict_claim2, $norm_claim3, $type_claim3, $strategy_claim3, $verdict_claim3,$norm_claim4, $type_claim4, $strategy_claim4, $verdict_claim4,$norm_claim5, $type_claim5, $strategy_claim5, $verdict_claim5, $total_time, $times, $date, $claim_id);

            if ($norm_claim2 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim2, $row['url_article'], $row['verdict_article']);
            }
            if ($norm_claim3 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim3, $row['url_article'], $row['verdict_article']);
            }
            if ($norm_claim4 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim4, $row['url_article'], $row['verdict_article']);
            }
            if ($norm_claim5 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim5, $row['url_article'], $row['verdict_article']);
            }

            $float_time = 0;
            if($total_time < 1200){
                $float_time = floatval($total_time);
            }
            update_table($conn, "UPDATE Annotators SET current_task=0, finished_claim_annotations=finished_claim_annotations+1, annotation_time = annotation_time + ?  WHERE user_id=?",'ii', $float_time, $user_id);
            $conn->commit();
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
        $conn->close();
    }

}else if ($req == "reload-claim"){
    $user_id = $_GET["user_id"];
    $servername = "localhost";
    $username = $db_params['user'];
    $password = $db_params['password'];
    $dbname = $db_params['database'];

    $back_count = $_GET['back_count'];# - 1;

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT claim_id, claim_text, url_previous_checker, transcription, norm_claim1, type_claim1, strategy_claim1,verdict_claim1, norm_claim2, type_claim2, strategy_claim2,verdict_claim2,norm_claim3, type_claim3, strategy_claim3,verdict_claim3,norm_claim4, type_claim4, strategy_claim4,verdict_claim4,norm_claim5, type_claim5, strategy_claim5,verdict_claim5,total_annotation_time, annotation_time_events, date_made
    FROM $claim_table WHERE user_id = ? AND date_made > DATE_SUB(CURDATE(), INTERVAL 1 DAY) ORDER BY date_made DESC LIMIT 1 OFFSET ?";

    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $back_count);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        echo json_encode(array($row['claim_id'], $row['claim_text'], $row['url_previous_checker'], $row['transcription'], $row['norm_claim1'], $row['type_claim1'], $row['strategy_claim1'], $row['verdict_claim1'], $row['norm_claim2'], $row['type_claim2'], $row['strategy_claim2'], $row['verdict_claim2'], $row['norm_claim3'], $row['type_claim3'], $row['strategy_claim3'], $row['verdict_claim3'], $row['norm_claim4'], $row['type_claim4'], $row['strategy_claim4'], $row['verdict_claim4'], $row['norm_claim5'], $row['type_claim5'], $row['strategy_claim5'], $row['verdict_claim5'], $row['total_annotation_time'], $row['annotation_time_events'], $row['date_made']));
    }else{
        echo json_encode(array(-1));
    }
}
else if ($req == "claim-resubmission"){
    $servername = "localhost";
    $username = $db_params['user'];
    $password = $db_params['password'];
    $dbname = $db_params['database'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_POST["user_id"];
    $claim_id = $_POST["claim _id"];

    $url_previous_checker = $_POST["url_previous_checker"];
    $transcription = $_POST["transcription"];

    $norm_claim1 = $_POST["norm_claim1"];
    $type_claim1 = $_POST["type_claim1"];
    $strategy_claim1 = $_POST["strategy_claim1"];
    $verdict_claim1 = $_POST["verdict_claim1"];

    $norm_claim2 = $_POST["norm_claim2"];
    $type_claim2 = $_POST["type_claim2"];
    $strategy_claim2 = $_POST["strategy_claim2"];
    $verdict_claim2 = $_POST["verdict_claim2"];

    $norm_claim3 = $_POST["norm_claim3"];
    $type_claim3 = $_POST["type_claim3"];
    $strategy_claim3 = $_POST["strategy_claim3"];
    $verdict_claim3 = $_POST["verdict_claim3"];

    $norm_claim4 = $_POST["norm_claim4"];
    $type_claim4 = $_POST["type_claim4"];
    $strategy_claim4 = $_POST["strategy_claim4"];
    $verdict_claim4 = $_POST["verdict_claim4"];

    $norm_claim5 = $_POST["norm_claim5"];
    $type_claim5 = $_POST["type_claim5"];
    $strategy_claim5 = $_POST["strategy_claim5"];
    $verdict_claim5 = $_POST["verdict_claim5"];

    $date_claim = $_POST["date_claim"];
    $times = $_POST['times'];
    $total_time = $_POST['total_time'];

    $sql = "SELECT claim_id FROM $claim_table WHERE claim_id = ? AND user_id = ?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ii",$claim_norm_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $err = "Error description, evidence resubmission get claim: ". $conn -> error;
    $row = $result->fetch_assoc();

    if($result->num_rows > 0){
        $float_time = floatval($total_time);

        $conn->begin_transaction();
        try {
            update_table($conn, "UPDATE $claim_table SET url_previous_checker=?,transcription=?, norm_claim1=?, type_claim1=?, strategy_claim1=?,verdict_claim1=?,norm_claim2=?, type_claim2=?, strategy_claim2=?, verdict_claim2=?, norm_claim3=?, type_claim3=?, strategy_claim3=?, verdict_claim3=?, norm_claim4=?, type_claim4=?, strategy_claim4=?,verdict_claim4=?, norm_claim5=?, type_claim5=?, strategy_claim5=?, verdict_claim5=?, 
                 total_annotation_time=?, annotation_time_events=?, date_made=?, taken_flag=0, norm_annotators_num = norm_annotators_num+1 WHERE claim_id=?", 'ssssssssssssssssssssssssi',
                $url_previous_checker, $transcription, $norm_claim1, $type_claim1, $strategy_claim1, $verdict_claim1, $norm_claim2, $type_claim2, $strategy_claim2, $verdict_claim2, $norm_claim3, $type_claim3, $strategy_claim3, $verdict_claim3,$norm_claim4, $type_claim4, $strategy_claim4, $verdict_claim4,$norm_claim5, $type_claim5, $strategy_claim5, $verdict_claim5, $total_time, $times, $date, $claim_id);

            if ($norm_claim2 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim2, $row['url_article'], $row['verdict_article']);
            }
            if ($norm_claim3 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim3, $row['url_article'], $row['verdict_article']);
            }
            if ($norm_claim4 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim4, $row['url_article'], $row['verdict_article']);
            }
            if ($norm_claim5 != 'null'){
                update_table($conn, "INSERT INTO $claim_table (claim_norm_text,url_article,verdict), SELECT ?, ?, ? FROM $qa_table", 'sss',
                    $norm_claim5, $row['url_article'], $row['verdict_article']);
            }

            $conn->commit();
        }catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            throw $exception;
        }
    }
    $conn -> close();
}
else{
    echo "Not found";
}

