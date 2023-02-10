const Contact = {
    update: function (id) {
        let form = $('#form_contact_'+id);
        let inputs = form.filter(':input');
        inputs.each(function() {
            this.removeAttr('disabled');
        });
    }
}
