<?php
//ini_set('session.gc_maxlifetime', 86400);
//session_set_cookie_params(86400);
//session_start();
// Start the session

?>

<html>
<head>
    <link rel="stylesheet" href="css/style_verdict_validation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

    <script src="js/extensions/jquery.js"></script>
    <script src="js/extensions/jquery.md5.js"></script>
    <script src="js/extensions/jquery_ui.js"></script>
    <script src="js/extensions/inputfit.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

    <script src="js/verdict_validation.js.js"></script>

    <meta name="viewport" content="width=device-width"/>
</head>

<body style="font-family: sans-serif">

<div class="topnav" id="myTopnav">
    <!--    <a href="user-details.php" id="user-details" class="fa fa-user-circle-o">User Details</a>-->
    <a href="annotation_guidelines/guideline.pdf" target="_blank" class="guidelines fa fa-file-pdf-o">Annotation Guidelines</a>
    <a href="" class="logout fa fa-sign-out" style="text-align:right">Logout</a>
</div>

<div class="menu-frame" id="my-menu-frame">
    <div class="claim-normalization-specifications">
        <button id= 'go-back' class="btn btn-primary fa fa-arrow-circle-left fa-lg pull-left button-responsive-info"></button>
        <button id= 'go-forward' class=" btn btn-primary fa fa-lg fa-arrow-circle-right pull-left button-responsive-info" disabled></button>
    </div>


    <div class="answer-div" id="answer-div">
        <span  id='claim-label' class="box-label">Claim Label:</span>
        <select id="claim-label-selector" class="selectpicker ">
            <option value="Select Label" selected disabled hidden>Select Label</option>
            <option value="Supported" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Supported</span>'>Supported</option>
            <option value="Refuted" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Refuted</span>'>Refuted</option>
            <option value="Not Enough Information" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Not Enough Information</span>'>Not Enough Information</option>
            <option value="Missing Context" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Missing Context</span>'>Missing Context</option>
        </select>
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="
        <ul>
        <li>Supported: The claim is fully supported by the arguments and evidence presented.
        <li>Refuted: The claim is fully contradicted by the arguments and evidence presented.
        <li>Not Enough Information: There is not enough information to support or refute the claim. The evidence either directly argues that appropriate evidence cannot be found, or leaves some aspect of the claim neither supported nor refuted.
        <li>Missing Context: The claim is misleading due to missing context, but not explicitly refuted. This includes cherry picking, true-but-misleading claims, as well as cases where conflicting or internally contradictory evidence can be found. Missing context may also be relevant if a situation has changed over time, and the claim fails to mention this."></button>

        <br>
        <br>


    <div id="submission-buttons">
        <button type='button' class="fa fa-check btn btn-success button-responsive" id='generated-claim-submit'> Submit Claims</button>
        <button type='button' class="fa fa-flag btn btn-warning button-responsive" id='generated-claim-skip'> Skip</button>
    </div>

</div>



</body>
</html>