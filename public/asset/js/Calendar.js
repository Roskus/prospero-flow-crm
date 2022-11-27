/**
 * @version 1.0.1
 * @type {{scheduleEvent: Window.Calendar.scheduleEvent, save: Window.Calendar.save}}
 */
window.Calendar = {
    scheduleEvent : function(startDate)
    {
        const date = document.getElementById('date');
        date.value = startDate;
        
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


        let token = document.getElementsByName('csrf-token')[0].getAttribute('content');
        let title = $('#title').val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let guests = $('#guests').val();
        let description = $('#description').val();
        let meeting = $('#meeting').val();

        //address
        //latitude
        //longitude

        let eventObj = {
            title : title,
            start_date: start_date,
            end_date: end_date,
            guests: guests,
            is_all_day: null, //@TODO
            description: description,
            meeting: meeting,
            _token: token
        }

        let postEventString = JSON.stringify(eventObj)
        fetch("/calendar/event/save", {
            method: 'post',
            body: postEventString,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json()
        }).then((res) => {
            if (res.status === 201) {
                console.log("Post successfully created!")

            }
        }).catch((error) => {
            console.log(error)
        });
        myModal.hide();
    }

}
