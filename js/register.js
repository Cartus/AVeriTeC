$( document ).ready(function() {
    $(document.body).on('click', '#login-button', function(){
        localStorage.clear();
        var login_name = $("#login-name").val();
        var login_pw = $("#login-pw").val();
        var login_pw_md5 = $.md5(login_pw);
        var mode = ($('input[name=mode]:checked').val());
        $.get('annotation-service/register.php', {name: login_name, pw: login_pw, pw_md5: login_pw_md5, mode: mode});
    })

    $("#login-pw").on('keypress', function(e){
        if (e.which == 13) {
            localStorage.clear();
            var login_name = $("#login-name").val();
            var login_pw = $("#login-pw").val();
            var login_pw_md5 = $.md5(login_pw);
            var mode = ($('input[name=mode]:checked').val());
            $.get('annotation-service/register.php', {name: login_name, pw: login_pw, pw_md5: login_pw_md5, mode: mode});
        }
    })
});

