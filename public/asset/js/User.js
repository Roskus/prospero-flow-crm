const User = {
    delete: function (id, message)
    {
        let response = confirm(message);
        if(response) {
            window.location.href = window.location.protocol+'//'+window.location.host + '/user/delete/'+id;
        }
    }
}
