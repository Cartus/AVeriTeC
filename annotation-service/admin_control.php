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
    $number_logins = 0;
    $finished_norm_annotations = 0;
    $finished_qa_annotations = 0;
    $finished_valid_annotations = 0;

    $conn->begin_transaction();
    try {
        update_table($conn, "INSERT INTO Annotators (user_name, password_cleartext, password_md5, is_admin, number_logins,
        finished_norm_annotations, finished_qa_annotations, finished_valid_annotations) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 'sssiiiii', 
        $user_name, $password, $password_md5, $is_admin, $number_logins, $finished_norm_annotations, $finished_qa_annotations, $finished_valid_annotations);
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
    $ids = implode("','", $user_ids_to_delete);
    $sql_del = "DELETE FROM Annotators WHERE user_id=IN ('".$ids."')";

    $stmt= $conn->prepare($sql_del);
    $stmt->bind_param("ii", $user_id);
    $stmt->execute();
    echo "Users Deleted!";
    $conn->close();
} else if ($req_type == "get-user") {
    $conn = new mysqli($db_params['servername'], $db_params['user'], $db_params['password'], $db_params['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT user_id, user_name, finished_norm_annotations, finished_qa_annotations, finished_valid_annotations FROM Annotators";
    $result = $conn->query($sql);
    
    $table = array();
    $counter = 0;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $table_row = array();
            $table_row["id"] = $row['user_id'];
            $table_row["user_name"] = $row['user_name'];
            $table_row["finished_norm_annotations"] = $row['finished_norm_annotations'];
            $table_row["finished_qa_annotations"] = $row['finished_qa_annotations'];
            $table_row["finished_valid_annotations"] = $row['finished_valid_annotations'];
            $table[$counter] = $table_row;
            $counter = $counter + 1;
        };
        echo(json_encode($table));
    } else {
        echo "0 Result";
    }

    $conn->close();
}


?>
