/**
 * @version 1.1.0
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
        // Hidden inputs keep their value after reset(), clear the previously opened event
        document.getElementById('calendar_event_id').value = '';
        document.getElementById('guest_list').innerHTML = '';
        $('#form_delete').removeClass("visible").addClass("invisible").removeAttr('action');
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
            $('#date').val((calendarEvent.start_date).match(regExDate)[0]);

            $('#end_date').val((calendarEvent.end_date).match(regExDate)[0]);

            $('#is_all_day').prop('checked', (calendarEvent.is_all_day == 1) ? true : false);

            $('#start_time').val((calendarEvent.start_date).match(regExTime)[0]);
            $('#end_time').val((calendarEvent.end_date).match(regExTime)[0]);
            $('#description').val(calendarEvent.description);
            $('#meeting').val(calendarEvent.meeting);
            $('#address').val(calendarEvent.address);

            document.getElementById('guest_list').innerHTML = '';
            (calendarEvent.guests || []).forEach((guest) => Calendar.appendGuest(guest));

            myModal.show();
        }).catch((error) => {
            console.log(error);
        });
    },
    addGuest: function ()
    {
        let input = document.getElementById('guest');
        let guest = input.value.trim();

        if (guest === '' || !input.checkValidity()) {
            input.reportValidity();
            return;
        }

        Calendar.appendGuest(guest);
        input.value = '';
        input.focus();
    },
    appendGuest: function (guest)
    {
        let list = document.getElementById('guest_list');
        let alreadyAdded = Array.from(list.querySelectorAll('input[name="guest_list[]"]'))
            .some((hidden) => hidden.value.toLowerCase() === guest.toLowerCase());

        if (alreadyAdded) {
            return;
        }

        let item = document.createElement('li');
        item.className = 'list-group-item d-flex justify-content-between align-items-center py-1';

        let label = document.createElement('span');
        label.textContent = guest;

        let hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'guest_list[]';
        hidden.value = guest;

        let remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-sm btn-link text-danger p-0';
        remove.innerHTML = '<i class="las la-times"></i>';
        remove.addEventListener('click', () => item.remove());

        item.append(label, hidden, remove);
        list.append(item);
    }
}

document.getElementById('event_form').addEventListener('submit', () => {
    let pendingGuest = document.getElementById('guest');

    if (pendingGuest.value.trim() !== '') {
        Calendar.appendGuest(pendingGuest.value.trim());
        pendingGuest.value = '';
    }
});
