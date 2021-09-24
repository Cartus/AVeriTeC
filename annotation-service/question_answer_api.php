<?php
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
session_start();

function update_table($conn, $sql_command, $types , ...$vars ) {
  $sql2 = $sql_command;
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

if ($_SESSION['annotation_phase'] != 'question_answering') {
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
        update_table($conn, "UPDATE $qa_table SET taken_flag=0, skipped=?, skipped_by=? WHERE claim_norm_id=?", 'sii', $report_text, $user_id, $claim_norm_id);//add reported by
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
      $output =  array($row['claim_norm_id'], $row['claim_norm_text'], $row['url_article']);
      echo json_encode($output);
    } else {
      $sql = "SELECT claim_norm_id, claim_norm_text, url_article FROM $qa_table WHERE qa_annotators_num = 0 AND taken_flag=0 AND skipped IS NULL ORDER BY RAND() LIMIT 1";
      $result = $conn->query($sql);
      $err = "Error description select claim 3: " . $conn -> error;
      $row = $result->fetch_assoc();
      $output =  array($row['claim_norm_id'], $row['claim_norm_text'], $row['url_article']);

      echo json_encode($output);

      $conn->begin_transaction();
      try {
        if(!is_null($row['id'])){
        update_table($conn, "UPDATE Annotators SET current_task=? WHERE user_id=?", 'ii', $row['claim_norm_id'], $user_id);
        update_table($conn, "UPDATE $qa_table SET taken_flag=1 WHERE claim_norm_id=?", 'i', $row['claim_norm_id']);
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
else if ($req == "qapairs-submission"){

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

  $question1 = $_POST['question1'];
  $answer1 = $_POST['answer1'];
  $type_answer1 = $_POST['type_answer1'];
  $url_answer1 = $_POST['url_answer1'];

  $question2 = $_POST['question2'];
  $answer2 = $_POST['answer2'];
  $type_answer2 = $_POST['type_answer2'];
  $url_answer2 = $_POST['url_answer2'];

  $question3 = $_POST['question3'];
  $answer3 = $_POST['answer3'];
  $type_answer3 = $_POST['type_answer3'];
  $url_answer3 = $_POST['url_answer3'];

  $question4 = $_POST['question4'];
  $answer4 = $_POST['answer4'];
  $type_answer4 = $_POST['type_answer4'];
  $url_answer4 = $_POST['url_answer4'];

  $question5 = $_POST['question5'];
  $answer5 = $_POST['answer5'];
  $type_answer5 = $_POST['type_answer5'];
  $url_answer5 = $_POST['url_answer5'];

  $times = $_POST['times'];
  $total_time = $_POST['total_time'];

  if ($question2 == 'null'){
    $question2 = NULL;
    $answer2 = NULL;
    $type_answer2 = NULL;
    $url_answer2 = NULL;
  }

  if ($question3 == 'null'){
    $question3 = NULL;
    $answer3 = NULL;
    $type_answer3 = NULL;
    $url_answer3 = NULL;
  }

  if ($question4 == 'null'){
    $question4 = NULL;
    $answer4 = NULL;
    $type_answer4 = NULL;
    $url_answer4 = NULL;
  }

  if ($question5 == 'null'){
    $question5 = NULL;
    $answer5 = NULL;
    $type_answer5 = NULL;
    $url_answer5 = NULL;
  }

  $sql = "SELECT claim_norm_id FROM $qa_table WHERE (claim_id = (SELECT current_task FROM Annotators WHERE user_id=?))";
  $stmt= $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $err = "Error description, qa submission get claim: ". $conn -> error;
  $row = $result->fetch_assoc();

  if($result->num_rows > 0){

    $float_time = floatval($total_time);

    $float_time_adjusted = 0;
    if($float_time < 1200){
      $float_time_adjusted = $float_time;
    }

    $conn->begin_transaction();
    try {
      update_table($conn, "INSERT INTO $qa_table(qa_user_id, claim_norm_id, question1, answer1, type_answer1, url_answer1, question2, answer2, type_answer2, url_answer2, question3, answer3, type_answer3, url_answer3, question4, answer4, type_answer4, url_answer4, question5, answer5, type_answer5, url_answer5, 
                  total_annotation_time, annotation_time_events, date_made) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?)",
          'iissssssssssss', $user_id, $claim_norm_id, $question1, $answer1, $type_answer1, $url_answer1, $question2, $answer2, $type_answer2, $url_answer2, $question3, $answer3, $type_answer3, $url_answer3, $question4, $answer4, $type_answer4, $url_answer4, $question5, $answer5, $type_answer5, $url_answer5,
          $total_time, $times, $date);
      update_table($conn, "UPDATE Annotators SET current_task=0, finished_qa_annotations=finished_qa_annotations + 1, annotation_time = annotation_time + ? WHERE user_id=?",'ii',$float_time_adjusted, $user_id);
      update_table($conn, "UPDATE $qa_table SET taken_flag=0, qa_annotators_num = qa_annotators_num+1 WHERE claim_norm_id=?", 'i', $claim_norm_id);
      $conn->commit();
    }catch (mysqli_sql_exception $exception) {
      $conn->rollback();
      throw $exception;
    }
  }
  $conn -> close();
}
else if ($req == "qa-resubmission"){

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

  $question1 = $_POST['question1'];
  $answer1 = $_POST['answer1'];
  $type_answer1 = $_POST['type_answer1'];
  $url_answer1 = $_POST['url_answer1'];

  $question2 = $_POST['question2'];
  $answer2 = $_POST['answer2'];
  $type_answer2 = $_POST['type_answer2'];
  $url_answer2 = $_POST['url_answer2'];

  $question3 = $_POST['question3'];
  $answer3 = $_POST['answer3'];
  $type_answer3 = $_POST['type_answer3'];
  $url_answer3 = $_POST['url_answer3'];

  $question4 = $_POST['question4'];
  $answer4 = $_POST['answer4'];
  $type_answer4 = $_POST['type_answer4'];
  $url_answer4 = $_POST['url_answer4'];

  $question5 = $_POST['question5'];
  $answer5 = $_POST['answer5'];
  $type_answer5 = $_POST['type_answer5'];
  $url_answer5 = $_POST['url_answer5'];

  $times = $_POST['times'];
  $total_time = $_POST['total_time'];

  if ($question2 == 'null'){
    $question2 = NULL;
    $answer2 = NULL;
    $type_answer2 = NULL;
    $url_answer2 = NULL;
  }

  if ($question3 == 'null'){
    $question3 = NULL;
    $answer3 = NULL;
    $type_answer3 = NULL;
    $url_answer3 = NULL;
  }

  if ($question4 == 'null'){
    $question4 = NULL;
    $answer4 = NULL;
    $type_answer4 = NULL;
    $url_answer4 = NULL;
  }

  if ($question5 == 'null'){
    $question5 = NULL;
    $answer5 = NULL;
    $type_answer5 = NULL;
    $url_answer5 = NULL;
  }

  $sql = "SELECT claim_norm_id FROM $qa_table WHERE claim_norm_id = ? AND qa_user_id = ?";
  $stmt= $conn->prepare($sql);
  $stmt->bind_param("ii",$claim_norm_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $err = "Error description, qa resubmission get claim: ". $conn -> error;
  $row = $result->fetch_assoc();

  if($result->num_rows > 0){
    $float_time = floatval($total_time);

    $conn->begin_transaction();
    try {
      update_table($conn, "UPDATE $qa_table SET question1=?, answer1=?, type_answer1=?, url_answer1=?, question2=?, answer2=?, type_answer2=?, url_answer2=?, question3=?, answer3=?, type_answer3=?, url_answer3=?, question4=?, answer4=?, type_answer4=?, url_answer4=?, question5=?, answer5=?, type_answer5=?, url_answer5=?, date_modified=? WHERE claim_norm_id = ?", 'sssssssssssssssssi',
          $question1, $answer1, $type_answer1, $url_answer1, $question2, $answer2, $type_answer2, $url_answer2, $question3, $answer3, $type_answer3, $url_answer3, $question4, $answer4, $type_answer4, $url_answer4, $question5, $answer5, $type_answer5, $url_answer5, $date, $row['claim_norm_id']);
      $conn->commit();
    }catch (mysqli_sql_exception $exception) {
      $conn->rollback();
      throw $exception;
    }
  }
  $conn -> close();
}
else if ($req == "reload-qa"){

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

  $sql = "SELECT claim_norm_id, claim_norm_text, question1, answer1, type_answer1, url_answer1, question2, answer2, type_answer2, url_answer2, question3, answer3, type_answer3, url_answer3, question4, answer4, type_answer4, url_answer4, question5, answer5, type_answer5, url_answer5, total_annotation_time, annotation_time_events FROM $qa_table WHERE qa_user_id = ? AND date_made > DATE_SUB(CURDATE(), INTERVAL 1 DAY) ORDER BY date_made DESC LIMIT 1 OFFSET ?";
  $stmt= $conn->prepare($sql);
  $stmt->bind_param("ii", $user_id, $back_count);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if($result->num_rows > 0){
    echo json_encode(array($row['claim_norm_id'], $row['claim_norm_text'], $row['question1'], $row['answer1'], $row['url_answer1'], $row['question2'], $row['answer2'], $row['url_answer2'], $row['question3'], $row['answer3'], $row['url_answer3'], $row['question4'], $row['answer4'], $row['url_answer4'], $row['question5'], $row['answer5'], $row['url_answer5'],
        $row['total_annotation_time'], $row['annotation_time_events']));
  }else{
    echo json_encode(array(-1));
  }
}
?>
