/**
 * Created by zhangjiali on 2018/11/13.
 */

$(document).ready(function(){
    function login(){
        $.get('/site/login',{
            email:$('#email').val(),
            password:$('#password').val(),
            auto:$('#auto').is(':checked')?true:false
        },function(json){
            if (json.r == 1) {
                window.location.href = '/media/input-list';
            } else {
                __BDP.alertBox("提示",json.msg);
            }
        },'json')
    }

    $('#submit').on('click',function(){
        login();
    });
    $('#email,#password').on('keypress',function(e){
        if(e.keyCode=='13'){
            login();
        }
    })
});
