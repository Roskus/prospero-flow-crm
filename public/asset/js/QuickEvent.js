/**
 * Reduced "add event" popup used on the dashboard calendar.
 * @version 1.0.0
 */
(function () {
    const modalElement = document.getElementById('quickEventModal');

    if (!modalElement) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('quick_event_form');
    const isAllDay = document.getElementById('quick_event_is_all_day');
    const startTime = document.getElementById('quick_event_start_time');
    const endTime = document.getElementById('quick_event_end_time');
    const moreOptions = document.getElementById('quick_event_more_options');

    function toggleTimes() {
        [startTime, endTime].forEach((input) => {
            input.readOnly = isAllDay.checked;
            input.required = !isAllDay.checked;
            if (isAllDay.checked) {
                input.value = '';
            }
        });
    }

    function nextHour() {
        const date = new Date();
        date.setHours(date.getHours() + 1, 0, 0, 0);

        return date;
    }

    function formatTime(date) {
        return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
    }

    isAllDay.addEventListener('change', toggleTimes);

    startTime.addEventListener('change', () => {
        if (!startTime.value || (endTime.value && endTime.value > startTime.value)) {
            return;
        }
        const [hours, minutes] = startTime.value.split(':').map(Number);
        const end = new Date();
        end.setHours(Math.min(hours + 1, 23), hours >= 23 ? 59 : minutes, 0, 0);
        endTime.value = formatTime(end);
    });

    form.addEventListener('submit', (event) => {
        if (!isAllDay.checked && endTime.value <= startTime.value) {
            event.preventDefault();
            endTime.setCustomValidity(endTime.dataset.invalidMessage || 'End time must be after start time');
            endTime.reportValidity();
        }
    });

    endTime.addEventListener('input', () => endTime.setCustomValidity(''));

    modalElement.addEventListener('shown.bs.modal', () => document.getElementById('quick_event_title').focus());

    window.QuickEvent = {
        open: function (date) {
            form.reset();
            toggleTimes();
            document.getElementById('quick_event_date').value = date;
            const start = nextHour();
            const end = new Date(start.getTime() + 60 * 60 * 1000);
            startTime.value = formatTime(start);
            endTime.value = start.getHours() === 23 ? '23:59' : formatTime(end);
            moreOptions.href = moreOptions.href.replace(/\/calendar.*$/, '/calendar/' + date);
            modal.show();
        }
    };
})();
