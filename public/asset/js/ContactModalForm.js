document.addEventListener("DOMContentLoaded", function () {
    let formContactModal = document.getElementById('formContactModal');
    formContactModal.addEventListener('show.bs.modal', showFormContactModal);

    let btnSaveForm = document.getElementById('btnSaveForm');
    btnSaveForm.addEventListener('click', saveForm);

    var formContact = document.getElementById('formContact');
});

function saveForm() {
    let formData = new FormData(formContact);
    let plainFormData = Object.fromEntries(formData.entries());
    let formDataJsonString = JSON.stringify(plainFormData);

    const fetchOptions = {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "Authorization": "Bearer ".concat(api_token)
        },
        body: formDataJsonString,
    };

    fetch(formContact.action, fetchOptions)
        .then((response) => response.json())
        .then((data) => {

            console.log(data);

            if (data.errors) {
                // TODO
                alert(Object.values(data.errors));
            }

            // TODO
            alert('The contact has been saved successfully.');
            location.reload();
        });
}


function showFormContactModal(event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-bs-id');

    if (id) {
        const fetchOptions = {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Bearer ".concat(api_token)
            }
        };

        // TODO
        document
            .querySelector('#formContactModal .modal-title')
            .insertAdjacentHTML('beforeend', '<span id="badge-loading" class="badge bg-danger p-3">LOADING.....</span>');

        fetch('/api/contact/'.concat(id), fetchOptions)
            .then((response) => response.json())
            .then((data) => {

                document.querySelector('input[name="id"]').value = data.id || '';
                document.querySelector('input[name="lead_id"]').value = data.lead_id || '';
                document.querySelector('input[name="customer_id"]').value = data.customer_id || '';
                document.querySelector('input[name="contact_first_name"]').value = data.first_name || '';
                document.querySelector('input[name="contact_last_name"]').value = data.last_name || '';
                document.querySelector('input[name="contact_phone"]').value = data.phone || '';
                document.querySelector('input[name="contact_mobile"]').value = data.mobile || '';
                document.querySelector('input[name="contact_email"]').value = data.email || '';
                document.querySelector('input[name="contact_linkedin"]').value = data.linkedin || '';
                document.querySelector('input[name="contact_job_title"]').value = data.job_title || '';
                document.querySelector('input[name="contact_notes"]').value = data.notes || '';
            })
            .catch((err) => {
                console.error(err);
            })
            .finally(() => {
                document.querySelector('#badge-loading').remove();
            });
    }
}
