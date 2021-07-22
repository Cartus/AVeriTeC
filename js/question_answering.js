$.ajaxSetup({async:false});

// $(window).on('load', function() {
//     var status_page = localStorage.getItem('status-page');
//     if (status_page == 'open'){
//         $('body').remove();
//     }else{
//         run_page();
//     }
// });

$(window).on('load', function() {
    run_page();
});




function log_out(){
    $.ajax({
        url: "annotation-service/logout.php",
        type: "GET",
        success: function(data){
            localStorage.clear();
        }
    });
}

function auto_logout(){
    var idleMax = 20; // Logout after 25 minutes of IDLE
    var idleTime = 0;

    var idleInterval = setInterval(timerIncrement, 60000);  // 1 minute interval
    $( "body" ).mousemove(function( event ) {
        idleTime = 0; // reset to zero
    });

    // count minutes
    function timerIncrement() {
        idleTime = idleTime + 1;
        if (idleTime > idleMax) {
            log_out();
            location.reload();
        }
    }
}

function run_page() {
    auto_logout();
    localStorage.setItem('status-page', 'open');
    $("body").append('<iframe id="my-wikipedia" ></iframe>');
    $("#my-wikipedia").prop('src', 'https://web.archive.org/web/20210717085246/https://www.factcheck.org/2021/07/cdc-data-thus-far-show-covid-19-vaccination-safe-during-pregnancy/');

    // $('#my-wikipedia').on("load", function() {});

    $(function () {
        $('[data-toggle="popover"]').popover();
    })

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(".logout").on('click', function(e) {
        log_out();
        window.location.reload();
    });
}

function add_text(div){
    var input = document.createElement('input');
    input.setAttribute('type', 'text');
    input.setAttribute('name', 'organizers[]');
    input.setAttribute('class', 'class-input');

    var btn = document.getElementById(div);
    btn.insertBefore(input, null);
}

function add_textarea(div){
    var input = document.createElement('textarea');
    input.setAttribute('class', 'class-input');

    var btn = document.getElementById(div);
    btn.insertBefore(input, null);
}



class Processor {
    static init(){
        Processor.get_data_for_claim_generation();
    }

    static get_data_for_claim_generation(){
        $.get('annotation-service/claim_annotation_api.php', { user_id: Processor.user_id, request: 'next-data'},function(data,status,xhr){
            if (status != 'success'){
                alert('Server problem');
            }else{
                Processor.data_id = data[0]; //used for sending off the annotation to server
                var data_url = data[1];
                var is_table = data[2];
                var claim_text = data[3];
                Processor.data_selected_id = data_selected_id;


                if (data_url === null){
                    data_url = 'NO NEW HIGHLIGHT AVAILABLE.'
                }

                Processor.is_table = is_table;
                Processor.data_url = data_url

                // $('#claim_url').html('<b>' + data_url + "</b>");
                // $('#manipulations').html("Mutated Claim <b>(" + manipulation + ")</b>:");


            }
        }, 'json');

    }
}