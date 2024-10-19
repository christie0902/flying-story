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
            eventDidMount: function (info) {
                //console.log("Event Object:", info.event);
                let categoryName = info.event.title;
                let capacity = info.event.extendedProps.capacity;
                let registeredStudents =
                    info.event.extendedProps.registered_students;
                let time = info.event.extendedProps.formattedTime;
                let userRegistrationStatus =
                    info.event.extendedProps.userRegistrationStatus;

                let registrationLabel = "";
                if (userRegistrationStatus === "Pending") {
                    registrationLabel =
                        '<span class="badge bg-primary w-md-50 d-block mx-auto my-1">Pending</span>';
                } else if (userRegistrationStatus === "Confirmed") {
                    registrationLabel =
                        '<span class="badge bg-light text-dark w-md-50 d-block mx-auto my-1">Enrolled</span>';
                }
                // Set inner HTML
                let capacityColorClass =
                    registeredStudents >= capacity ? "text-success" : "";
                info.el.innerHTML = `
                    <div class="event-content">
                        <b>${time} ${categoryName} ${
                    info.event.extendedProps.status === "canceled"
                        ? '<span style="color: rgb(235 104 100);">(Canceled)</span>'
                        : ""
                }</b>
                        <span class="capacity-info ${capacityColorClass}">(Spots: ${registeredStudents}/${capacity})</span>
                        ${registrationLabel}
                    </div>
                `;
                if (info.event.extendedProps.status === "canceled") {
                    info.el.style.backgroundColor = "#c9c7c5";
                } else {
                    info.el.style.backgroundColor =
                        info.event.extendedProps.eventBgColor || "#007bff";
                }

                info.el.style.color = "#5e452a";
                info.el.style.margin = "2px 0";
            },
            eventClick: function (info) {
                // Canceled event
                if (info.event.extendedProps.status === "canceled") {
                    return;
                }
                fetch(`/calendar/lesson/${info.event.id}`)
                    .then((response) => response.json())
                    .then((data) => {
                        const lesson = data.lesson;
                        //console.log(lesson.img_url)
                        const lessonDateTime = new Date(lesson.schedule);
                        const currentTime = Date.now();

                        // Cover the case 8 hours before class starts
                        const hoursDifference =
                            (lessonDateTime.getTime() - currentTime) /
                            (1000 * 60 * 60); // Difference in hours
                        const isWithin8Hours = hoursDifference < 8;

                        const lessonIsPassed =
                            lessonDateTime.getTime() < currentTime;

                        // PRICE DISPLAY
                        const price = document.getElementById("priceContainer");
                        if (price) {
                                    if (
                                        lesson.payment_info.type === "credits"
                                    ) {
                                        price.textContent = 'By credit';
                                        
                                    } else {
                                        price.textContent =
                                            lesson.payment_info.price;
                                    }
                        }
                        // Lesson Cover Img
                        const coverImgElement = document.getElementById('class-coverImg');
                        coverImgElement.src = lesson.img_url; //can also set default image here

                        // Display Policies
                        const policyContainer = document.getElementById(
                            "class-policy"
                        );

                        if (policyContainer && lesson.payment_info && lesson.payment_info.type === "credits") {
                            policyContainer.style.display = "block";
                        } else {
                            policyContainer.style.display = "none";
                        }
                      

                        const capacity = lesson.capacity;
                        const registeredStudents = lesson.registered_students;
                        const availableSpots = capacity - registeredStudents;

                        document.getElementById("lessonTitle").textContent =
                            lesson.title;
                        document.getElementById("lessonCategory").textContent =
                            lesson.category;
                        document.getElementById("lessonSchedule").textContent =
                            lesson.schedule;
                        document.getElementById("lessonDuration").textContent =
                            lesson.duration;
                        document.getElementById(
                            "availableSpots"
                        ).textContent = `${availableSpots} (Enrolled: ${registeredStudents}/${capacity})`;

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
                        const cancelWarning =
                            document.getElementById("cancelWarning");

                        if (lessonPassedMessage)
                            lessonPassedMessage.style.display = lessonIsPassed
                                ? "block"
                                : "none";
                        if (joinForm)
                            joinForm.style.display =
                                lessonIsPassed || lesson.user_is_registered
                                    ? "none"
                                    : "block";
                        if (cancelForm)
                            cancelForm.style.display = lessonIsPassed
                                ? "none"
                                : lesson.user_is_registered
                                ? "block"
                                : "none";

                        //Disable cancelation 8 hours before starting time
                        if (cancelForm) {
                            const cancelButton = cancelForm.querySelector(
                                "button[type='submit']"
                            );
                            if (
                                !lessonIsPassed &&
                                isWithin8Hours &&
                                lesson.user_is_registered
                            ) {
                                if (cancelButton) cancelButton.disabled = true;
                                cancelWarning.style.display = "block";
                            } else {
                                if (cancelButton) cancelButton.disabled = false;
                                cancelWarning.style.display = "none";
                            }
                        }
                        // Full lesson
                        const fullJoinWarning =
                            document.getElementById("joinWarning");
                        if (
                            (registeredStudents >= capacity) &&
                            !lessonIsPassed && 
                            !lesson.user_is_registered
                        ) {
                            if (joinForm)
                                joinForm.querySelector(
                                    "button[type='submit']"
                                ).disabled = true;
                            if (fullJoinWarning)
                                fullJoinWarning.style.display = "block";
                        } else {
                            if (joinForm)
                                joinForm.querySelector(
                                    "button[type='submit']"
                                ).disabled = false;
                            if (fullJoinWarning) fullJoinWarning.style.display = "none";
                        }

                        // Admin edit button
                        const editBtn = document.getElementById("editButton");
                        if (editBtn)
                            editBtn.setAttribute(
                                "href",
                                `/admin/lessons/show/${lesson.id}`
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

                        // Workshop button
                        const workshopButtonContainer =
                            document.querySelector(".workshop-button");
                        const stdBtnContainer = document.querySelector(
                            "#join-cancel-container"
                        );

                        //Buy credits button
                        const creditBtn =
                            document.getElementById("buyCreditsButton");
                        if (creditBtn) {
                            const baseUrl = "/buy-credits";
                            const urlWithLesson = lesson.id
                                ? `${baseUrl}/${lesson.id}`
                                : baseUrl;

                            creditBtn.setAttribute("href", urlWithLesson);
                        }

                        // Workshop Join Button
                        const savedContainerStyle = stdBtnContainer
                            ? { ...stdBtnContainer.style }
                            : {};
                        
                        const additionalInfo = document.querySelector('.additional-info');
                        if (additionalInfo) additionalInfo.style.display = 'none';

                        if (
                            lesson.category.toLowerCase() === "workshop" &&
                            workshopButtonContainer &&
                            !lessonIsPassed
                        ) {
                            workshopButtonContainer.setAttribute(
                                "class",
                                "workshop-button d-flex justify-content-center"
                            );

                            const joinWkspBtn = document.createElement("a");
                            joinWkspBtn.setAttribute(
                                "class",
                                "btn btn-primary px-5"
                            );
                            
                            workshopButtonContainer.appendChild(joinWkspBtn);
                            stdBtnContainer.setAttribute(
                                "style",
                                "display: none !important"
                            );

                            if (lesson.user_is_registered){
                                joinWkspBtn.innerText = "Enrolled";
                                joinWkspBtn.classList.add('disabled')
                                joinWkspBtn.style.marginTop = "5px";
                                additionalInfo.style.display = 'block';
                                additionalInfo.style.marginTop = "5px";
                                additionalInfo.innerText = 'You have signed up for this workshop. Please contact us if you would like to cancel.'

                            } else {
                                joinWkspBtn.innerText = "Join Class";
                                joinWkspBtn.classList.remove('disabled');
                                joinWkspBtn.style.marginTop = "5px";
                            }

                            joinWkspBtn.setAttribute(
                                "href",
                                `/join-class/${lesson.id}`
                            );
                        }

                        const modalElm =
                            document.getElementById("lessonDetailsModal");

                        const modal = new bootstrap.Modal(modalElm);

                        const resetContainerStyle = () => {
                            modalElm.removeEventListener(
                                "hidden.bs.modal",
                                resetContainerStyle
                            );
                            if (!stdBtnContainer || !workshopButtonContainer)
                                return;
                            stdBtnContainer.style = { ...savedContainerStyle };
                            workshopButtonContainer.setAttribute(
                                "class",
                                "workshop-button d-none"
                            );
                            const wkspBtn =
                                workshopButtonContainer.querySelector(".btn");
                            if (workshopButtonContainer.querySelector(".btn"))
                                workshopButtonContainer.removeChild(wkspBtn);
                        };
                        modal.show();
                        modalElm.addEventListener(
                            "hidden.bs.modal",
                            resetContainerStyle
                        );
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
