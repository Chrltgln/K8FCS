document.addEventListener("DOMContentLoaded", () => {
  const pages = document.querySelectorAll(".form-page");
  const nextButtons = document.querySelectorAll('[id^="next-page-"]');
  const prevButtons = document.querySelectorAll('[id^="prev-page-"]');
  const ownershipSelect = document.getElementById("ownership");
  const ownershipOtherInput = document.getElementById("ownership-other");
  const incomeSourceRadios = document.getElementsByName("income_source");
  const incomeSourceOtherInput = document.getElementById("income-source-other");
  const incomeSourceOtherInput2 = document.getElementById("income-source-other2");

  let currentPage = 0;

  // Function to show the current page and hide others
  function showPage(pageIndex) {
    pages.forEach((page, index) => {
      page.style.display = index === pageIndex ? "block" : "none";
    });
  }

  // Event listener for Next buttons
  nextButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (currentPage < pages.length - 1) {
        currentPage++;
        showPage(currentPage);
      }
    });
  });

  // Event listener for Previous buttons
  prevButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (currentPage > 0) {
        currentPage--;
        showPage(currentPage);
      }
    });
  });

  // Event listener for Ownership select
  ownershipSelect.addEventListener("change", () => {
    if (ownershipSelect.value === "Others") {
      ownershipOtherInput.style.display = "block";
    } else {
      ownershipOtherInput.style.display = "none";
    }
  });

  // Event listener for Source of Income radios
  incomeSourceRadios.forEach((radio) => {
    radio.addEventListener("change", () => {
      if (document.getElementById("other-income").checked) {
        incomeSourceOtherInput.style.display = "block";
        incomeSourceOtherInput2.style.display = "none";
      } else {
        incomeSourceOtherInput.style.display = "none";
        incomeSourceOtherInput2.style.display = "block";
      }
    });
  });

  // Initialize form by showing the first page
  showPage(currentPage);

  // Check for success flag in session storage and show success message
  if (sessionStorage.getItem('formSuccess') === 'true') {
    Swal.fire({
      title: "Success!",
      text: "Your application has been successfully submitted. Please check your email for further updates.",
      icon: "success",
      confirmButtonText: "OK"
    }).then((result) => {
      if (result.isConfirmed) {
        // Clear the success flag from session storage
        sessionStorage.removeItem('formSuccess');
        // Redirect to homepage.php
        window.location.href = '../clientside/homepage.php';
      }
    });
  }
});

$(document).ready(function () {
  var timeSelect = $("#appointment-time");

  // Function to populate time slots with 30-minute intervals
  function populateTimeSlots() {
    // Clear any existing options to prevent duplication
    timeSelect.empty();
    // Add default option
    timeSelect.append(new Option("Select a time", ""));
    for (var hour = 9; hour <= 17; hour++) {
      var time = ("0" + hour).slice(-2) + ":00";
      timeSelect.append(new Option(time, time));
      if (hour < 17) {
        var halfHour = ("0" + hour).slice(-2) + ":30";
        timeSelect.append(new Option(halfHour, halfHour));
      }
    }
  }

  // Initial population of time slots
  populateTimeSlots();

  $("#appointment-date").on("change", function () {
    var appointmentDate = $(this).val();
    if (appointmentDate) {
      var date = new Date(appointmentDate);
      var day = date.getUTCDay();

      // Check if the selected date is a weekend (Saturday or Sunday)
      if (day === 6 || day === 0) {
        timeSelect.empty();
        timeSelect.append(new Option("Office is closed", ""));
        return;
      } else {
        // Re-enable all options if a valid weekday is selected
        populateTimeSlots();
      }

      // Remove past time slots if the selected date is today
      var today = new Date();
      if (date.toDateString() === today.toDateString()) {
        var currentTime = today.getHours() + ":" + ("0" + today.getMinutes()).slice(-2);
        timeSelect.find("option").each(function () {
          if ($(this).val() !== "" && $(this).val() < currentTime) {
            $(this).remove();
          }
        });
      }

      $.ajax({
        type: "POST",
        url: "process/fetch_appointments.php",
        data: { appointment_date: appointmentDate },
        success: function (response) {
          var takenSlots = JSON.parse(response);
          var maxClients = 1;
          var takenTimes = takenSlots
            .filter((slot) => slot.count >= maxClients)
            .map((slot) => slot.appointment_time.slice(0, 5)); // Extract HH:MM format

          timeSelect.find("option").each(function () {
            var optionValue = $(this).val();
            if (takenTimes.includes(optionValue)) {
              $(this).remove();
            }
          });

          // Check if there are no available time slots
          if (timeSelect.find("option").length === 1) {
            timeSelect.empty();
            timeSelect.append(new Option("Office is closed", ""));
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", status, error);
        },
      });
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const footer = document.querySelector('.footer');
  const mainContent = document.querySelector('.main-content');

  // Function to move dynamically added divs above the footer
  function moveDivsAboveFooter() {
      const dynamicDivs = document.querySelectorAll('body > div:not(.loader-wrapper):not(.main-content):not(.footer)');
      dynamicDivs.forEach(div => {
          mainContent.appendChild(div);
      });
  }

  // Create a MutationObserver to watch for changes in the body
  const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
          if (mutation.addedNodes.length) {
              moveDivsAboveFooter();
          }
      });
  });

  // Start observing the body for child list changes
  observer.observe(document.body, { childList: true });

  // Initial call to move any existing dynamic divs
  moveDivsAboveFooter();
});