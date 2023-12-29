/**
 * Email
 *
 * @param int id
 * @param string message
 */
window.ProspectFlow.Email = {
    duplicate: function (id, message) {
        let email = prompt(message);
        if(email) {
            window.location = window.location.origin + '/email/duplicate?email_id=' + id + '&to=' + email;
        }
    }
};
