$(document).ready(function(){$('#username').focus()});
function check_login(){
    $.ajax({
        type:'POST',
        url:'check_login.php',
        data:"username="+$('#username').val()+"&password="+$('#password').val(),
        success:function(response){
            //if(response=='1'){$('#error').css({'color':'#0c0','display':'block'}).html('CORRECT!')}
            if(response=='1'){self.location='index.php?login=1&data='+$('#username').val()}
            else if(response=='2'){$('#error').css({'color':'red','display':'block'}).html('Login credentials incorrect!')}
            else if(response=='3'){$('#error').css({'color':'red','display':'block'}).html('Please fill in all fields')}
        }
    });
};