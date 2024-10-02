import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import bootstrapPlugin from '@fullcalendar/bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';


document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const categorySelect = document.getElementById('categorySelect');

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin, bootstrapPlugin],
            initialView: 'dayGridMonth',
            height: '80vh',
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
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            editable: false,
            selectable: true,
            // eventContent: function(info) {
            //     let schedule = info.event.start;
            //     // let time = schedule.split(" ")[1].substring(0, 5);
            //     let categoryName = info.event.title;
            //     let capacity = info.event.extendedProps.capacity;
            //     let registeredStudents = info.event.extendedProps.registered_students;
            //     return { html: `<b>${schedule} ${categoryName}</b><br>(${registeredStudents}/${capacity})` };
            // },
            // eventRender: function(info) {
            //     let categoryName = info.event.title;
            //     let capacity = info.event.extendedProps.capacity;
            //     let registeredStudents = info.event.extendedProps.registered_students;

            //     // Create custom HTML element for the event
            //     let eventElement = document.createElement('div');
            //     eventElement.innerHTML = `<b>${categoryName}</b><br>(${registeredStudents}/${capacity})`;
            //     eventElement.style.backgroundColor = info.event.color;
            //     eventElement.style.color = '#fff';
            //     return eventElement;
            // },
            eventDidMount: function(info) {
                console.log('Event Object:', info.event);
                let categoryName = info.event.title;
                let capacity = info.event.extendedProps.capacity;
                let registeredStudents = info.event.extendedProps.registered_students;
                let time = info.event.extendedProps.formattedTime;
            
                // Set inner HTML
                info.el.innerHTML = `
                    <div class="event-content">
                        <b>${time} ${categoryName} ${info.event.extendedProps.status === "canceled" ? "(Canceled)" : ""}</b>
                        <span class="capacity-info">(Spots: ${registeredStudents}/${capacity})</span>
                    </div>
                `;
                if (info.event.extendedProps.status === "canceled") {
                    info.el.style.backgroundColor = '#dad6d3';
                } else {
                    
                    info.el.style.backgroundColor = info.event.extendedProps.eventBgColor || '#007bff';
                }
                
                info.el.style.color = '#5e452a';
                info.el.style.margin = '2px 0';
            },
            eventClick: function(info) {
                fetch(`/calendar/lesson/${info.event.id}`)
                    .then(response => response.json())
                    .then(data => {
                        const lesson = data.lesson;
                        
                        document.getElementById('lessonTitle').textContent = lesson.title;
                        document.getElementById('lessonCategory').textContent = lesson.category;
                        document.getElementById('lessonSchedule').textContent = lesson.schedule;
                        document.getElementById('lessonDuration').textContent = lesson.duration;
                        document.getElementById('lessonPrice').textContent = lesson.price;
                        document.getElementById('lessonCapacity').textContent = lesson.capacity;
                        document.getElementById('lessonRegisteredStudents').textContent = lesson.registered_students;
                        document.getElementById('lessonStatus').textContent = lesson.status;
                        document.getElementById('lessonDescription').textContent = lesson.description;

                        // Admin edit button
                        const editBtn = document.getElementById('editButton');
                        if(editBtn) editBtn.setAttribute('href', `/admin/lessons/edit/${lesson.id}`);
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
