/**
 * Prospero Flow CRM
 */
/* Tooltip global bootstrap: false */
(function () {
    'use strict';
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
})();

window.ProspectFlow = {
    exit: function(exit_text) {
        let response = confirm(exit_text);
        if(response) document.getElementById('logout-form').submit();
    }
};

document.addEventListener("DOMContentLoaded", function() {
    let delayMinutes = 5;
    let lastFetchNotificationTime = localStorage.getItem('ProspectFlowLastFetchNotificationTime');

    if(lastFetchNotificationTime === null){
        lastFetchNotificationTime = Date.now();
        localStorage.setItem('ProspectFlowLastFetchNotificationTime', lastFetchNotificationTime);

        ProspectFlow.Notification.getLatest();
    }

    let timeElapsed = Date.now() - lastFetchNotificationTime;
    let minutesElapsed = timeElapsed / 60000;

    if(minutesElapsed > delayMinutes) {
        ProspectFlow.Notification.getLatest();
        localStorage.setItem('ProspectFlowLastFetchNotificationTime', Date.now());
    }
});
