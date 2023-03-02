/* Tooltip global bootstrap: false */
(function () {
    'use strict'
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })
})()

window.Hammer = {

    exit: function(exit_text) {
        response = confirm(exit_text);
        if(response) document.getElementById('logout-form').submit();
    }
}

window.ProspectFlow = window.Hammer;

document.addEventListener("DOMContentLoaded", function() {
    var delayMinutes = 5;
    var lastFetchNotificationTime = localStorage.getItem('ProspectFlowLastFetchNotificationTime');
    
    if(lastFetchNotificationTime === null){
        lastFetchNotificationTime = Date.now();
        localStorage.setItem('ProspectFlowLastFetchNotificationTime', lastFetchNotificationTime);        
        
        ProspectFlow.Notification.getLatest();
    }

    var timeElapsed = Date.now() - lastFetchNotificationTime;
    var minutesElapsed = timeElapsed / 60000;

    if(minutesElapsed > delayMinutes){

        ProspectFlow.Notification.getLatest();

        localStorage.setItem('ProspectFlowLastFetchNotificationTime', Date.now());
    }
});
