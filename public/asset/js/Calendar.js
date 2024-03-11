/**
 * @version 1.0.1
 * @type {{scheduleEvent: Window.Calendar.scheduleEvent, save: Window.Calendar.save}}
 */

const options = {
    focus: true
};

const myModal = new bootstrap.Modal(document.getElementById('sheduleEventModal'), options);

window.Calendar = {
    scheduleEvent : function(startDate)
    {
        document.getElementById('event_form').reset();
        let date = document.getElementById('date');
        date.value = startDate;

        myModal.show();
    },
    read: function(id)
    {
        fetch( "/calendar/event/update/" + id, {
            method: 'get',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((data) => {
            let calendarEvent = data.calendar;
            let regExDate = /^\d{4}-\d{2}-\d{2}/;
            let regExTime = /\d{2}:\d{2}/;
            $('#form_delete').removeClass("invisible")
                .addClass("visible")
                .prop('action', route_calendar_controller + "/event/delete/" + calendarEvent.id);
            $('#calendar_event_id').val(calendarEvent.id);

            $('#title').val(calendarEvent.title);

            $('#end_date').val((calendarEvent.end_date).match(regExDate)[0]);

            $('#is_all_day').prop('checked', (calendarEvent.is_all_day == 1) ? true : false);

            $('#start_time').val((calendarEvent.start_date).match(regExTime)[0]);
            $('#end_time').val((calendarEvent.end_date).match(regExTime)[0]);
            $('#description').val(calendarEvent.description);
            $('#meeting').val(calendarEvent.meeting);
            $('#address').val(calendarEvent.address);

            let guest_list = $("#guest_list");
            for(let i = 0; i < calendarEvent.guests.length; i++) {
                let option = document.createElement("option");
                let guest = calendarEvent.guests[i];
                option.textContent = guest;
                option.value = guest;
                guest_list.append(option);
            }

            myModal.show();
        }).catch((error) => {
            console.log(error);
        });
    },
    addGuest: function ()
    {
        let guest = $("#guest").val();
        if(guest != '') {
            let guest_list = $("#guest_list");
            let option = document.createElement("option");
            option.textContent = guest;
            option.value = guest;
            guest_list.append(option);
        }
    }
}
