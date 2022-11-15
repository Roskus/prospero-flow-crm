window.Calendar = {
    scheduleEvent : function(startDate)
    {
        console.log('Schedule: '+startDate);
        const options = {
            focus: true
        }
        const myModal = new bootstrap.Modal(document.getElementById('sheduleEventModal'), options);
        myModal.show();
    },
    save: function()
    {
        const options = {
            focus: true
        }
        const myModal = new bootstrap.Modal(document.getElementById('sheduleEventModal'), options);
        myModal.hide();
        //@TODO

    }

}
