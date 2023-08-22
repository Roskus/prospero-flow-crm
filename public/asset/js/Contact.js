const Contact = {
    update: function (id) {

    },
    delete: function (id, message)
    {
        let response = confirm(message);
        if(response) {
            window.location.href = window.location.protocol+'//'+window.location.host + '/contact/delete/'+id;
        }
    }
};
