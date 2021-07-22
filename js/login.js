$( document ).ready(function() {
    $(document.body).on('click', '#login-button', function(){
        localStorage.clear();
        var login_name = $("#login-name").val();
        var login_pw = $("#login-pw").val();
        var login_pw_md5 = $.md5(login_pw);
        document.write(login_name);
        document.write(login_pw_md5);

        $.get('annotation-service/login.php', {name: login_name, pw: login_pw_md5},function(data,status,xhr){
            var success = data[0];
            var annotation_type = data[1];
            if (success == 0){
                $('.login-input').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                msg = "Invalid username and password!";
                $("#message").html(msg);
            }
            else{
                localStorage.setItem("annotation-type", annotation_type);
                localStorage.setItem("user", login_name);
                localStorage.setItem('first-load', 'true');
                location.reload();
            }
        }, 'json');
    })

    $("#login-pw").on('keypress', function(e){
        if (e.which == 13) {
            localStorage.clear();
            var login_name = $("#login-name").val();
            var login_pw = $("#login-pw").val();
            var login_pw_md5 = $.md5(login_pw);
            document.write(login_name);
            document.write(login_pw_md5);

            $.get('annotation-service/login.php', {name: login_name, pw: login_pw_md5},function(data,status,xhr){
                success = data[0];
                annotation_type = data[1];
                if (success == 0){
                    $('.login-input').attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                    msg = "Invalid username and password!";
                    $("#message").html(msg);
                }else{
                    localStorage.setItem("annotation-type", annotation_type);
                    localStorage.setItem("user", login_name);
                    localStorage.setItem('first-load', 'true');
                    location.reload();
                }
            }, 'json');
        }
    })
});