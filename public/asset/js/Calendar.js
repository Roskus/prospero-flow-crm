/**
 * @version 1.0.1
 * @type {{scheduleEvent: Window.Calendar.scheduleEvent, save: Window.Calendar.save}}
 */

const options = {
    focus: true
}
const myModal = new bootstrap.Modal(document.getElementById('sheduleEventModal'), options);

window.Calendar = {
    scheduleEvent : function(startDate)
    {
        let date = document.getElementById('date');
        date.value = startDate;

        myModal.show();
    },
    read: function(id)
    {
        fetch("/calendar/event/update/" + id, {
            method: 'get',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json()
        }).then((data) => {
            $('#id').val(data.id); 
            $('#title').val(data.title); 
            
            var regEx = /^\d{4}-\d{2}-\d{2}/;
            $('#end_date').val((data.end_date).match(regEx)[0]);

            $('#is_all_day').prop('checked', (data.is_all_day == 1) ? true : false);            

            var regEx = /\d{2}:\d{2}/;
            $('#start_time').val((data.start_date).match(regEx)[0]);

            var regEx = /\d{2}:\d{2}/;
            $('#end_time').val((data.end_date).match(regEx)[0]);
            
            $('#guests').val(data.guests);
            $('#description').val(data.description);
            $('#meeting').val(data.meeting);
            $('#address').val(data.address);        

            myModal.show();
        }).catch((error) => {
            console.log(error)
        });
    }
}
