function remoteLogin(name,pass,token,url) {
    $('#remoteLoginForm > .admin_name').val(name);
    $('#remoteLoginForm > .admin_pass').val(pass);
    $('#remoteLoginForm > .userToken').val(token);
    $('#remoteLoginForm').attr('action',url+'/'+REMOTELOGINADDR);

    $('#remoteLoginForm').submit();
}