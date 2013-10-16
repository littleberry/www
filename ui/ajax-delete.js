$(document).ready();
function check_projects(){
    $.ajax({
        type:'POST',
        url:'delete_only.php',
        data:"client_id="+$('#client_id').val(),
        success:function(response){
            if(response=='0'){
            $('#error').css({'color':'#0c0','display':'block'}).html('You successfully deleted the client.')
            }
            else if(response=='1'){
            $('#error').css({'color':'red','display':'block'}).html('You may not delete a client with active projects.')
            }
        }
    });
};