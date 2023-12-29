const Supplier = {
    delete: function (id, message)
    {
        let response = confirm(message);
        if(response) {
            window.location.href = window.location.protocol+'//'+window.location.host + '/supplier/delete/'+id;
        }
    }
}
