<?php
//ini_set('session.gc_maxlifetime', 86400);
//session_set_cookie_params(86400);
//session_start();
// Start the session

?>

<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_claim_normalization.css">
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

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />

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

    <div id="claim_entry_container"></div>


    <script type="text/javascript">

    </script>

    <div class="meta-claim-div" id="meta-claim-div">

        <span  id='claim-hyperlink' class="box-label">Hyperlink:</span>
        <input type="text"  spellcheck="true" class='hyperlink-claim' id='hyperlink-claim' value="">
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="A hyperlink to the original claim, if that is provided by the fact checking site. Examples of this include Facebook posts, the original article or blog post being fact checked, and embedded video links. If the original claim has a hyperlink on the fact checking site, but that hyperlink is dead, annotators should leave the field empty."></button>

        <span  id='claim-date' class="box-label">Date:</span>
        <input type="text"  spellcheck="true" class='date-claim' id='date-claim' value="">
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="The date of the original claim, regardless of whether it is necessary for verifying the claim. This date is often mentioned by the fact checker, but not in a standardized place where we could automatically retrieve it. Note that the date of origin for the original claim and the fact checking article may be different and both stated in text. We specifically need the original claim date, as we intend to filter out results published after that date during search. Furthermore, that date may be necessary for checking the claim."></button>

        <span  id='claim-speaker' class="box-label">Speaker:</span>
        <input type="text"  spellcheck="true" class='speaker-claim' id='speaker-claim' value="">
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="The speaker (or source) of the original claim. This will also help resolve ambiguities when producing questions."></button>

        <span  id='claim-transcription' class="box-label">Transcription:</span>
        <input type="text"  spellcheck="true" class='transcription-claim' id='transcription-claim' value="">
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="If the original source is an image that contains text (for example, the Facebook meme about Michelle Obama listed above), we ask the annotators to transcribe whatever text occurs in the image as metadata. This is an easy way to add additional training data for anyone wishing to build models without an image processing component, and should not take much extra time for the annotators to gather."></button>

        <br>
        <br>

        <span  id='claim type' class="box-label">Claim Type:</span>
        <select id="claim-type-selector" class="selectpicker ">
            <option value="Select Type" selected disabled hidden>Select Type</option>
            <option value="Speculative Claim" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Speculative Claim</span>'>Speculative Claim</option>
            <option value="Numerical Claim" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Numerical Claim</span>'>Numerical Claim</option>
            <option value="Position Statement" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Position Statement</span>'>Position Statement</option>
            <option value="Event/Property Claim" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Event/Property Claim</span>'>Event/Property Claim</option>
            <option value="Doctored Media Identification" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Doctored Media Identification</span>'>Doctored Media Identification</option>
            <option value="Complex Media Claim" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Complex Media Claim'>Complex Media Claim</option>
        </select>
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="The type of the claim itself, independent of the approach taken by the fact checker to verify or refute it.
        <ul>
        <li>Speculative Claims: such as “the price of crude oil will rise next year.” The primary task is to assess whether the prediction is plausible or realistic. This is beyond the scope of this project, and we will discard all such claims.
        <li>Opinion Claims: such as “cannabis should be legalized”. This contrasts with factual claims on the same topic, such as “legalization of cannabis has helped reduce opioid deaths.” This type of claim belongs to an opinion that is not factual. We will discard all claims in this category
        <li>Numerical Claims: the primary task is to verify whether a numerical fact is true, to verify whether a comparison between several numerical facts hold, or to determine whether a numerical trend or correlation is supported by the evidence.
        <li>Quote Verification: the primary task is to identify whether a quote was actually said by the supposed speaker.
        <li>Position Statements: the primary task is to identify whether a public figure has taken a certain position, e.g. supporting a particular policy or idea.
        <li>Event/Property Claims: the primary task is to determine the veracity of a narrative about a particular event or series of events, or to identify whether a certain non-numerical property is true, e.g. a person attending a particular university.
        <li>Doctored Media Identification: the primary task is to determine whether an image, video, or soundbite has been doctored. This also includes HTML-doctoring of social media posts. We will discard all claims in this category.
        <li>Complex Media Claims. The primary task is to perform complex reasoning about pieces of media, distinct from doctoring. This could for example be geolocating an image, or analysing audio. "></button>

        <span  id='claim-strategy' class="box-label">Fact Checking Strategy:</span>
        <select id="claim-strategy-selector" class="selectpicker ">
            <option value="Select Strategy" selected disabled hidden>Select Strategy</option>
            <option value="Written Evidence" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Speculative Claim</span>'>Speculative Claim</option>
            <option value="Numerical Comparison" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Numerical Comparison</span>'>Numerical Comparison</option>
            <option value="Consultation" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Consultation</span>'>Consultation</option>
            <option value="Satirical Source Identification" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Satirical Source Identification</span>'>Satirical Source Identification</option>
            <option value="Image Analysis" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Image Analysis</span>'>Image Analysis</option>
            <option value="Other Media Analysis" data-content='<span data-toggle="tooltip" data-placement="right" title=".">Other Media Analysis'>Other Media Analysis</option>
        </select>
        <button type="button" class="btn btn-sm btn-info small h-5 fa fa-info rounded-circle button-responsive-info" data-toggle="popover" data-html="true"  data-content="Classify the approach taken by the fact checker:
        <ul>
        <li>Written Evidence: The fact checking process involved finding contradicting written evidence, e.g. a news article directly refuting the claim.
        <li>Numerical Comparison: The fact checking process involved numerical comparisons, such as verifying that one number is greater than another.
        <li>Consultation: The fact checkers directly reached out to relevant experts or people involved with the story, reporting new information from such sources as part of the fact checking article.
        <li>Satirical Source Identification: The fact checking process involved identifying the source of the claim as satire, e.g. The Onion. We will discard all claims that were refuted only through satirical source identification.
        <li>Image Analysis: The fact checking process involved image analysis, such as comparing two images.
        <li>Other media analysis: The fact checking process involved analysing other media, such as video."></button>

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

    </div>

    <div class="split-claim-div" id="split-claim-div">
        <span  id='claim-split' class="box-label">Claim Split:</span>
        <input type="button" onclick="add('split-claim-div');" value="Add Claim" />
    </div>


    <div class="norm-claim-div" id="norm-claim-div">
        <span  id='claim-norm' class="box-label">Claim Normalization:</span>
        <input type="text"  spellcheck="true" class='class-input' id='date-claim' value="">
        <input type="button" onclick="add('norm-claim-div');" value="Add Claim" />
    </div>


    <div id="submission-buttons">
        <button type='button' class="fa fa-check btn btn-success button-responsive" id='generated-claim-submit'> Submit Claims</button>
        <button type='button' class="fa fa-flag btn btn-warning button-responsive" id='generated-claim-skip'> Skip</button>
    </div>

</div>



    <script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>

    <script type="module" src="client/claim_normalization/metadata_entry_bar.js"></script>

</body>
</html>