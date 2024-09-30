import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var categorySelect = document.getElementById('categorySelect');

    if (calendarEl) {
        var calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            events: function(fetchInfo, successCallback, failureCallback) {
                // Get selected category
                let selectedCategory = categorySelect.value;

                // Fetch events based on selected category
                fetch(`/calendar/load-events?category=${selectedCategory}`)
                    .then(response => response.json())
                    .then(events => successCallback(events))
                    .catch(error => failureCallback(error));
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            editable: true,
            selectable: true,
            eventContent: function(info) {
                // Customize event display
                let categoryName = info.event.title;
                let capacity = info.event.extendedProps.capacity;
                let registeredStudents = info.event.extendedProps.registered_students;
                return { html: `<b>${categoryName}</b><br>(${registeredStudents}/${capacity})` };
            },
            eventClick: function(info) {
                fetch(`/calendar/lesson/${info.event.id}`)
                    .then(response => response.json())
                    .then(lesson => {
                        document.getElementById('lessonTitle').textContent = lesson.title;
                        document.getElementById('lessonCategory').textContent = lesson.category.name;
                        document.getElementById('lessonSchedule').textContent = lesson.schedule;
                        document.getElementById('lessonDuration').textContent = lesson.duration;
                        document.getElementById('lessonPrice').textContent = lesson.price;
                        document.getElementById('lessonCapacity').textContent = lesson.capacity;
                        document.getElementById('lessonRegisteredStudents').textContent = lesson.registered_students;
                        document.getElementById('lessonStatus').textContent = lesson.status;
                        document.getElementById('lessonDescription').textContent = lesson.description;

                        const modal = new bootstrap.Modal(document.getElementById('lessonDetailsModal'));
                        modal.show();
                    });
            }
        });

        calendar.render();

        // Event listener for category select change
        categorySelect.addEventListener('change', function() {
            calendar.refetchEvents(); // Re-fetch events when filter changes
        });
    }
});
