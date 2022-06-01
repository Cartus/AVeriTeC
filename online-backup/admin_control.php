<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type');

function update_table($conn, $sql_command, $types, ...$vars)
{
    $sql2 = $sql_command;
    $stmt =  $conn->prepare($sql2);
    $stmt->bind_param($types, ...$vars);
    $stmt->execute();
}

$db_params = parse_ini_file( dirname(__FILE__).'/db_params.ini', false);


$json_result = file_get_contents("php://input");
$_POST = json_decode($json_result, true);

$user_id = $_POST['user_id'];
$req_type = $_POST['req_type'];

if ($req_type == "add-user") {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $password_md5 = $_POST['password_md5'];

    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $is_admin = 0;
    $is_active = 1;
    $number_logins = 0;

    $finished_norm_annotations = 0;
    $finished_qa_annotations = 0;
    $finished_valid_annotations = 0;
    $finished_dispute_annotations = 0;
    $finished_post_annotations = 0;

    $skipped_norm_data = 0;
    $skipped_qa_data = 0;

    $p1_time_sum = 0;
    $p1_load_sum = 0;
    $p2_time_sum = 0;
    $p2_load_sum = 0;
    $p3_time_sum = 0;
    $p4_time_sum = 0;
    $p4_load_sum = 0;
    $p5_time_sum = 0;

    $p1_timed_out = 0;
    $p2_timed_out = 0;
    $p4_timed_out = 0;
    
    $p1_speed_trap = 0;
    $p2_speed_trap = 0;
    $p3_speed_trap = 0;
    $p4_speed_trap = 0;
    $p5_speed_trap = 0;

    $conn->begin_transaction();
    try {
        update_table($conn, "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, is_active, number_logins,
        finished_norm_annotations, finished_qa_annotations, finished_valid_annotations, finished_dispute_annotations, finished_post_annotations,  
        skipped_norm_data, skipped_qa_data,
        p1_time_sum, p1_load_sum, p2_time_sum, p2_load_sum, p3_time_sum, p4_load_sum, p4_time_sum, p5_time_sum, 
        p1_timed_out, p2_timed_out, p4_timed_out,
        p1_speed_trap, p2_speed_trap, p3_speed_trap, p4_speed_trap, p5_speed_trap)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'sssiiiiiiiiiiiiiiiiiiiiiiiiii',
        $user_name, $password, $password_md5, $is_admin, $is_active, $number_logins, $finished_norm_annotations, $finished_qa_annotations,
        $finished_valid_annotations, $finished_dispute_annotations, $finished_post_annotations, 
        $skipped_norm_data, $skipped_qa_data, $p1_time_sum, $p1_load_sum, $p2_time_sum, 
        $p2_load_sum, $p3_time_sum, $p4_load_sum, $p4_time_sum, $p5_time_sum, 
        $p1_timed_out, $p2_timed_out, $p4_timed_out,
        $p1_speed_trap, $p2_speed_trap, $p3_speed_trap, $p4_speed_trap, $p5_speed_trap);

        $conn->commit();
        echo "User Added!";
    }catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
    $conn->close();
} else if ($req_type == "remove-users") {
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_ids_to_delete = $_POST['user_ids_to_delete'];
    foreach ($user_ids_to_delete as $del_id) {
        $sql_del = "UPDATE Annotators SET is_active=0 WHERE user_id=$del_id";
        $stmt= $conn->prepare($sql_del);
        $stmt->execute();
    }

    echo "Users Deleted!";
    $conn->close();
} else if ($req_type == "get-user") {
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM Annotators WHERE is_active=1";
    $result = $conn->query($sql);

    $table = array();
    $counter = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $table_row = array();
            $table_row["id"] = $row['user_id'];
            $table_row["is_admin"] = (int)$row['is_admin'];
            $table_row["user_name"] = $row['user_name'];

            $table_row["finished_norm_annotations"] = $row['finished_norm_annotations'];
            $table_row["finished_qa_annotations"] = $row['finished_qa_annotations'];
            $table_row["finished_valid_annotations"] = $row['finished_valid_annotations'];
            $table_row["finished_p4"] = $row['finished_dispute_annotations'];
            $table_row["finished_p5"] = $row['finished_post_annotations'];

            $table_row["p1_assigned"] = $row['p1_assigned'];
            $table_row["p2_assigned"] = $row['p2_assigned'];
            $table_row["p3_assigned"] = $row['p3_assigned'];
            $table_row["p4_assigned"] = $row['p4_assigned'];
            $table_row["p5_assigned"] = $row['p5_assigned'];

            $p1_task_time = round($row['p1_time_sum'] / max($row['finished_norm_annotations'], 1), 2);
            $p2_task_time = round($row['p2_time_sum'] / max($row['finished_qa_annotations'], 1), 2);
            $p3_task_time = round($row['p3_time_sum'] / max($row['finished_valid_annotations'], 1), 2);
            $p4_task_time = round($row['p4_time_sum'] / max($row['finished_dispute_annotations'], 1), 2);
            $p5_task_time = round($row['p5_time_sum'] / max($row['finished_post_annotations'], 1), 2);

            $table_row["p1_task_time"] = $p1_task_time;
            $table_row["p2_task_time"] = $p2_task_time;
            $table_row["p3_task_time"] = $p3_task_time;
            $table_row["p4_task_time"] = $p4_task_time;
            $table_row["p5_task_time"] = $p5_task_time;
            
            $table_row["total_hours"] = round(($p1_task_time + $p2_task_time + $p3_task_time + $p4_task_time + $p5_task_time) / 60, 2);

            $table_row["pages_skipped"] = $row['skipped_norm_data'] + $row['skipped_qa_data'];

            $table_row["pages_timed_out"] = $row['p1_timed_out'] + $row['p2_timed_out'] + $row['p4_timed_out'];

            $table_row["speed_traps_hit"] = $row['p1_speed_trap'] + $row['p2_speed_trap'] + $row['p3_speed_trap'] + $row['p4_speed_trap'] + $row['p5_speed_trap'];

            $table_row['average_questions_p2'] = round($row['questions_p2'] / max($row['finished_qa_annotations'], 1), 2);
            $table_row['average_questions_p4'] = round($row['questions_p4'] / max($row['finished_dispute_annotations'], 1), 2);
            $table[$counter] = $table_row;
            $counter = $counter + 1;
        };
        echo(json_encode($table));
    } else {
        echo "0 Result";
    }
    $conn->close();
} else if ($req_type == "edit-users") {
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id_to_edit = $_POST['user_id_to_edit'];
    $target_field = $_POST['target_field'];
    $target_value = $_POST['target_value'];

    if ($target_field == "user_name") {
        update_table($conn, "UPDATE Annotators SET user_name=? WHERE user_id=?", 'si', $target_value, $user_id_to_edit);
        $conn->close();
    } else if ($target_field == "is_admin") {
        echo $target_value;
        update_table($conn, "UPDATE Annotators SET is_admin=? WHERE user_id=?", 'ii', $target_value, $user_id_to_edit);
        $conn->close();
    }
} 


?>
