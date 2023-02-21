const Contact = {
    update: function (id) {
        let form = $('#form_contact_'+id);
        let inputs = form.filter(':input');
        inputs.each(function() {
            this.removeAttr('disabled');
        });
    },
    delete: function (id, message)
    {
        let response = confirm(message);
        if(response) {
            window.location.href = window.location.protocol+'//'+window.location.host + '/contact/delete/'+id;
        }
    }
}
