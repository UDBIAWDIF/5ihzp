function collect(name,pass,token,url) {
    $('#collectionForm > .url').val(url);
    $('#collectionForm > .site').val(name);
    $('#collectionForm > .userToken').val(token);

    $('#collectionForm').submit();
}