<?php
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
session_start();

function update_table($conn, $sql_command, $types , ...$vars ) {
  $sql2 = $sql_command; // Add flag that current claim is taken. Need to be freed when evidence is submitted,
  $stmt= $conn->prepare($sql2);
  $stmt->bind_param($types, ...$vars);
  $stmt->execute();
}


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$date = date("Y-m-d H:i:s");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $req = $_POST["request"];
}else if ($_SERVER['REQUEST_METHOD'] === 'GET'){
  $req = $_GET["request"];
}else{
  $req = $argv[1];
}

$user_id = $_SESSION['user'];

$qa_table = "Qapairs";
//$claim_table = "Claims";

if ($_SESSION['annotation_phase'] != 'verdict_validation') {
  echo -2;
  return;
}

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);
$annotations_params = parse_ini_file( dirname(__FILE__).'/annotation_params.ini', false);

if ($req == "report-claim"){
  $claim_norm_id = $_POST["claim_norm_id"];
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

  //echo $row[1];
  if($result->num_rows > 0){
    if ($row['current_task'] != 0) {
      $sql = "SELECT claim_norm_id, claim_norm_text FROM $qa_table WHERE claim_norm_id = ?";
      $stmt= $conn->prepare($sql);
      $stmt->bind_param("i", $row['current_task']);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      $report_text = $_POST['report_text'];
      $report_text = implode(" [SEP] ", json_decode($report_text));

      $conn->begin_transaction();
      try {
        update_table($conn, "UPDATE $qa_table SET valid_taken_flag=0, valid_skipped=?, valid_skipped_by=? WHERE claim_norm_id=?", 'sii', $report_text, $user_id, $claim_norm_id);//add reported by
        update_table($conn, "UPDATE Annotators SET current_task=0, reported_claims = reported_claims + 1 WHERE user_id=?", 'i', $user_id);
        $conn->commit();
      }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
      }
    }
  }
  $conn->close();
}
else if ($req == "next-claim"){
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
      $sql = "SELECT claim_norm_id, claim_norm_text, url_article FROM $qa_table WHERE claim_norm_id = ?";
      $stmt= $conn->prepare($sql);
      $stmt->bind_param("i", $row['current_task']);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      $output =  array($row['claim_norm_id'], $row['claim_norm_text'], $row['question1'], $row['answer1'], $row['question2'], $row['answer2'], $row['question3'], $row['answer3'], $row['question4'], $row['answer4'], $row['question5'], $row['answer5']);
      echo json_encode($output);
    } else {
      $sql = "SELECT claim_norm_id, claim_norm_text, question1, answer1, question2, answer2, question3, answer3, question4, answer4, question5, answer5 FROM $qa_table WHERE valid_annotators_num = 0 AND valid_taken_flag=0 AND valid_skipped IS NULL ORDER BY RAND() LIMIT 1";
      $result = $conn->query($sql);
      $err = "Error description select claim 3: " . $conn -> error;
      $row = $result->fetch_assoc();
      $output =  array($row['claim_norm_id'], $row['claim_norm_text'], $row['question1'], $row['answer1'], $row['question2'], $row['answer2'], $row['question3'], $row['answer3'], $row['question4'], $row['answer4'], $row['question5'], $row['answer5']);
      echo json_encode($output);

      $conn->begin_transaction();
      try {
        if(!is_null($row['id'])){
        update_table($conn, "UPDATE Annotators SET current_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
        update_table($conn, "UPDATE $qa_table SET valid_taken_flag=1 WHERE claim_norm_id=?", 'i', $row['claim_norm_id']);
      }
        $conn->commit();
      }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
      }
      $conn->close();
    }
  }
}
else if ($req == "valid-submission"){

  $servername = "localhost";
  $username = $db_params['user'];
  $password = $db_params['password'];
  $dbname = $db_params['database'];

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $user_id = $_POST["user_id"];
  $claim_norm_id = $_POST["claim_norm_id"];
  $validated_verdict =  $_POST["validated_verdict"];

  $incorrect_answer1 = $_POST['incorrect_answer1'];
  $incorrect_answer2 = $_POST['incorrect_answer2'];
  $incorrect_answer3 = $_POST['incorrect_answer3'];
  $incorrect_answer4 = $_POST['incorrect_answer4'];
  $incorrect_answer5 = $_POST['incorrect_answer5'];

  $times = $_POST['times'];
  $total_time = $_POST['total_time'];

  if ($incorrect_answer2 == 'null'){
    $incorrect_answer2 = NULL;
  }

  if ($incorrect_answer3 == 'null'){
    $incorrect_answer3 = NULL;
  }

  if ($incorrect_answer4 == 'null'){
    $incorrect_answer4 = NULL;
  }

  if ($incorrect_answer5 == 'null'){
    $incorrect_answer5 = NULL;
  }


  $sql = "SELECT claim_norm_id FROM $qa_table WHERE (claim_id = (SELECT current_task FROM Annotators WHERE user_id=?))";
  $stmt= $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $err = "Error description, validation submission get claim: ". $conn -> error;
  $row = $result->fetch_assoc();

  if($result->num_rows > 0){

    $float_time = floatval($total_time);

    $float_time_adjusted = 0;
    if($float_time < 1200){
      $float_time_adjusted = $float_time;
    }

    $conn->begin_transaction();
    try {
      update_table($conn, "INSERT INTO $qa_table(valid_user_id, claim_norm_id, validated_verdict, incorrect_answer1, incorrect_answer2, incorrect_answer3, incorrect_answer4, incorrect_answer5, 
                  total_annotation_time, annotation_time_events, date_made) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'iisssssssss', $user_id, $claim_norm_id, $incorrect_answer1, $incorrect_answer2, $incorrect_answer3, $incorrect_answer4, $incorrect_answer5, $total_time, $times, $date);
      update_table($conn, "UPDATE Annotators SET current_task=0, finished_valid_annotations=finished_valid_annotations + 1, annotation_time = annotation_time + ? WHERE user_id=?",'ii',$float_time_adjusted, $user_id);
      update_table($conn, "UPDATE $qa_table SET taken_flag=0, valid_annotators_num = valid_annotators_num+1 WHERE claim_norm_id=?", 'i', $claim_norm_id);
      $conn->commit();
    }catch (mysqli_sql_exception $exception) {
      $conn->rollback();
      throw $exception;
    }
  }
  $conn -> close();
}
else if ($req == "valid-resubmission"){

  $servername = "localhost";
  $username = $db_params['user'];
  $password = $db_params['password'];
  $dbname = $db_params['database'];

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $user_id = $_POST["user_id"];
  $claim_norm_id = $_POST["claim_norm_id"];
  $validated_verdict =  $_POST["validated_verdict"];

  $incorrect_answer1 = $_POST['incorrect_answer1'];
  $incorrect_answer2 = $_POST['incorrect_answer2'];
  $incorrect_answer3 = $_POST['incorrect_answer3'];
  $incorrect_answer4 = $_POST['incorrect_answer4'];
  $incorrect_answer5 = $_POST['incorrect_answer5'];

  $times = $_POST['times'];
  $total_time = $_POST['total_time'];

  if ($incorrect_answer2 == 'null'){
    $incorrect_answer2 = NULL;
  }

  if ($incorrect_answer3 == 'null'){
    $incorrect_answer3 = NULL;
  }

  if ($incorrect_answer4 == 'null'){
    $incorrect_answer4 = NULL;
  }

  if ($incorrect_answer5 == 'null'){
    $incorrect_answer5 = NULL;
  }

  $sql = "SELECT claim_norm_id FROM $qa_table WHERE claim_norm_id = ? AND valid_user_id = ?";
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
      update_table($conn, "UPDATE $qa_table SET validated_verdict=?, incorrect_answer1=?, incorrect_answer2=?, incorrect_answer3=?, incorrect_answer4=?, incorrect_answer5=?, date_modified=? WHERE claim_norm_id = ?",
          'sssssssi', $validated_verdict, $incorrect_answer1, $incorrect_answer2, $incorrect_answer3, $incorrect_answer4, $incorrect_answer5, $date, $row['claim_norm_id']);
      $conn->commit();
    }catch (mysqli_sql_exception $exception) {
      $conn->rollback();
      throw $exception;
    }
  }
  $conn -> close();
}
else if ($req == "reload-valid"){

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

  $sql = "SELECT claim_norm_id, claim_norm_text, validated_verdict, incorrect_answer1, incorrect_answer2, incorrect_answer3, incorrect_answer4, incorrect_answer5, total_annotation_time, annotation_time_events FROM $qa_table WHERE valid_user_id = ? AND date_made > DATE_SUB(CURDATE(), INTERVAL 1 DAY) ORDER BY date_made DESC LIMIT 1 OFFSET ?";
  $stmt= $conn->prepare($sql);
  $stmt->bind_param("ii", $user_id, $back_count);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if($result->num_rows > 0){
    echo json_encode(array($row['claim_norm_id'], $row['claim_norm_text'], $row['incorrect_answer1'], $row['incorrect_answer2'], $row['incorrect_answer3'], $row['incorrect_answer4'], $row['incorrect_answer5'], $row['total_annotation_time'], $row['annotation_time_events']));
  }else{
    echo json_encode(array(-1));
  }
}
?>
