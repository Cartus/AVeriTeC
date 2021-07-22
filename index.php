<?php
//ini_set('session.gc_maxlifetime', 86400);
//session_set_cookie_params(86400);
session_start();

include 'question_answering.php';
echo "<!--";
//if (isset($_SESSION["user"])){
//  if($_SESSION['annotation_mode'] == 'claim_normalization'){
//     include 'claim_normalization.php';
//     echo "<!--";
//  }else if ($_SESSION['annotation_mode'] == 'qustion_answering'){
//      include 'question_answering.php';
//      echo "<!--";
//  }else if ($_SESSION['annotation_mode'] == 'verdict_validation'){
//      include 'verdict_validation.php';
//      echo "<!--";
//  }
//}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

    <script src="js/extensions/jquery.js"></script>
    <script src="js/extensions/jquery.md5.js"></script>
    <script src="js/extensions/jquery_ui.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="js/login.js"></script>
    <style>
        .login-form {
            width: 340px;
            margin: 50px auto;
            font-size: 15px;
        }
        .login-form form {
            margin-bottom: 15px;
            background: #f7f7f7;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .login-form h2 {
            margin: 0 0 15px;
        }
        .form-control, .btn {
            min-height: 38px;
            border-radius: 2px;
        }
        .btn {
            font-size: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="login-form">
    <form  method="post">
        <h2 class="text-center">Log in</h2>
        <p> This is the annotation tool for AVeriTeC. Please enter the credentials that have been given to you. <p/>
        <div class="form-group">
            <input type="text"  id='login-name' class="form-control" placeholder="Name" required="required">
        </div>
        <div class="form-group">
            <input type="password" id='login-pw' class="form-control" placeholder="Password" required="required">
        </div>
        <div class="form-group">
            <button type="button" id='login-button' class="btn btn-primary btn-block">Log in</button>
        </div>
    </form>
</div>
</body>
</html>