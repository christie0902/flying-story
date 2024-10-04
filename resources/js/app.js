import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import listPlugin from "@fullcalendar/list";
import bootstrapPlugin from "@fullcalendar/bootstrap";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "../css/app.css";

document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const categorySelect = document.getElementById("categorySelect");

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [
                dayGridPlugin,
                timeGridPlugin,
                interactionPlugin,
                listPlugin,
                bootstrapPlugin,
            ],
            initialView: "dayGridMonth",
            height: "80vh",
            events: function (fetchInfo, successCallback, failureCallback) {
                // Get selected category
                let selectedCategory = categorySelect.value;

                // Fetch events based on selected category
                fetch(`/calendar/load-events?category=${selectedCategory}`)
                    .then((response) => response.json())
                    .then((events) => successCallback(events))
                    .catch((error) => failureCallback(error));
            },
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,listMonth",
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
            eventDidMount: function (info) {
                //console.log("Event Object:", info.event);
                let categoryName = info.event.title;
                let capacity = info.event.extendedProps.capacity;
                let registeredStudents =
                    info.event.extendedProps.registered_students;
                let time = info.event.extendedProps.formattedTime;

                // Set inner HTML
                info.el.innerHTML = `
                    <div class="event-content">
                        <b>${time} ${categoryName} ${
                    info.event.extendedProps.status === "canceled"
                        ? "(Canceled)"
                        : ""
                }</b>
                        <span class="capacity-info">(Spots: ${registeredStudents}/${capacity})</span>
                    </div>
                `;
                if (info.event.extendedProps.status === "canceled") {
                    info.el.style.backgroundColor = "#dad6d3";
                } else {
                    info.el.style.backgroundColor =
                        info.event.extendedProps.eventBgColor || "#007bff";
                }

                info.el.style.color = "#5e452a";
                info.el.style.margin = "2px 0";
            },
            eventClick: function (info) {
                fetch(`/calendar/lesson/${info.event.id}`)
                    .then((response) => response.json())
                    .then((data) => {
                        const lesson = data.lesson;

                        const lessonDateTime = new Date(lesson.schedule);
                        const lessonIsPassed = lessonDateTime < Date.now();
                        
                        document.getElementById("lessonTitle").textContent =
                            lesson.title;
                        document.getElementById("lessonCategory").textContent =
                            lesson.category;
                        document.getElementById("lessonSchedule").textContent =
                            lesson.schedule;
                        document.getElementById("lessonDuration").textContent =
                            lesson.duration;
                        document.getElementById("paymentTerm").textContent =
                            lesson.payment_type;
                        document.getElementById("lessonCapacity").textContent =
                            lesson.capacity;
                        document.getElementById(
                            "lessonRegisteredStudents"
                        ).textContent = lesson.registered_students;
                        document.getElementById("lessonStatus").textContent =
                            lesson.status;
                        document.getElementById(
                            "lessonDescription"
                        ).textContent = lesson.description;

                        
                        //Passed lesson
                        // Show/Hide Cancel button based on whether the user is registered
                        const lessonPassedMessage = document.querySelector(
                            "#lessonPassedMessage p"
                        );
                        const joinForm = document.getElementById("joinForm");
                        const cancelForm =
                            document.getElementById("cancelForm");

                        if (lessonPassedMessage) lessonPassedMessage.style.display = lessonIsPassed ? "block" : "none";                       
                        if (joinForm) joinForm.style.display =
                            lessonIsPassed || lesson.user_is_registered
                                ? "none"
                                : "block";
                        if(cancelForm) cancelForm.style.display = lessonIsPassed
                            ? "none"
                            : lesson.user_is_registered
                            ? "block"
                            : "none";

                        // Admin edit button
                        const editBtn = document.getElementById("editButton");
                        if (editBtn)
                            editBtn.setAttribute(
                                "href",
                                `/admin/lessons/edit/${lesson.id}`
                            );

                        // Update the Join Form's action and hidden input field
                        if (joinForm)
                            joinForm.setAttribute(
                                "action",
                                `/lessons/${lesson.id}/register`
                            );
                        const lessonIdInput =
                            document.getElementById("joinLessonId");
                        if (lessonIdInput) lessonIdInput.value = lesson.id;

                        // Update the Cancel Form's action and hidden input field
                        if (cancelForm)
                            cancelForm.setAttribute(
                                "action",
                                `/lessons/${lesson.id}/cancel`
                            );
                        const lessonIdCancel =
                            document.getElementById("joinLessonId");
                        if (lessonIdCancel) lessonIdCancel.value = lesson.id;

                        
                        const workshopButtonContainer = document.querySelector('.workshop-button')
                        const stdBtnContainer = document.querySelector("#join-cancel-container");
                        
                        const savedContainerStyle = stdBtnContainer? {...stdBtnContainer.style} : {}

                        if (lesson.category.toLowerCase() === "workshop" && workshopButtonContainer && !lessonIsPassed) {
                            workshopButtonContainer.setAttribute("class","workshop-button d-flex justify-content-center");
                            const joinWkspBtn = document.createElement('div');
                            joinWkspBtn.setAttribute("class","btn btn-primary px-5");
                            joinWkspBtn.innerText = "Join Workshop";
                            workshopButtonContainer.appendChild(joinWkspBtn);
                            stdBtnContainer.setAttribute("style", "display: none !important");
                        }

                        const modalElm = document.getElementById("lessonDetailsModal");
                        
                        const modal = new bootstrap.Modal(modalElm);
                        
                        const resetContainerStyle = () => {
                            modalElm.removeEventListener('hidden.bs.modal', resetContainerStyle)
                            if (!stdBtnContainer || !workshopButtonContainer) return;
                            stdBtnContainer.style = {...savedContainerStyle};
                            workshopButtonContainer.setAttribute("class","workshop-button d-none");
                            const wkspBtn = workshopButtonContainer.querySelector('.btn');
                            if (workshopButtonContainer.querySelector('.btn')) workshopButtonContainer.removeChild(wkspBtn);
                        }
                        modal.show();
                        modalElm.addEventListener('hidden.bs.modal', resetContainerStyle)
                    });
            },
        });

        calendar.render();

        // Event listener for category select change
        categorySelect.addEventListener("change", function () {
            calendar.refetchEvents(); // Re-fetch events when filter changes
        });
    }
});
