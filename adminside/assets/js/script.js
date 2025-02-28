// SIDEBAR TOGGLE

let sidebarOpen = false;
const sidebar = document.getElementById("sidebar");

function openSidebar() {
  if (!sidebarOpen) {
    sidebar.classList.add("sidebar-responsive");
    sidebarOpen = true;
  }
}

function closeSidebar() {
  if (sidebarOpen) {
    sidebar.classList.remove("sidebar-responsive");
    sidebarOpen = false;
  }
}

// View details toggle
function toggleCard(cardId) {
  // Close any currently open card
  const openCards = document.querySelectorAll(".card-content");
  openCards.forEach((card) => {
    if (card.id !== cardId) {
      card.style.display = "none";
    }
  });

  const cardContent = document.getElementById(cardId);
  if (cardContent.style.display === "block") {
    cardContent.style.display = "none"; // Hide
  } else {
    cardContent.style.display = "block"; // Show
  }
}
document.addEventListener("DOMContentLoaded", function () {
  var today = new Date().toISOString().split("T")[0];
  var maturityInput = document.getElementById("maturity");
  var checkReleaseInput = document.getElementById("check-release");

  maturityInput.setAttribute("min", today);
  checkReleaseInput.setAttribute("min", today);

  function validateDates() {
    if (maturityInput.value === checkReleaseInput.value) {
      checkReleaseInput.setCustomValidity(
        "Maturity date and Check Release date must not be the same."
      );
    } else {
      checkReleaseInput.setCustomValidity("");
    }
  }

  maturityInput.addEventListener("change", validateDates);
  checkReleaseInput.addEventListener("change", validateDates);
});
//pagination automatic close previously opened

function toggleCardforViewAllClientDetails(cardId) {
  // Get all card contents
  const allCards = document.querySelectorAll(".card-content");
  let cardContentToToggle = document.getElementById(cardId);
  let infoCardToToggle = cardContentToToggle.closest(".info-card");

  // Check if the clicked card is already open
  if (cardContentToToggle.style.display === "block") {
    // If it is open, close it
    cardContentToToggle.style.display = "none";
    infoCardToToggle.classList.remove("expanded");
  } else {
    // Hide all other open cards and remove the expanded class
    allCards.forEach((card) => {
      card.style.display = "none";
      card.closest(".info-card").classList.remove("expanded");
    });
    // Show the selected card and add the expanded class
    cardContentToToggle.style.display = "block";
    infoCardToToggle.classList.add("expanded");
  }
}

function filterClients() {
  const input = document.getElementById("search-input").value.toLowerCase();
  const cards = document.querySelectorAll(".info-card");

  cards.forEach((card) => {
    const clientName = card
      .querySelector(".card-title")
      .textContent.toLowerCase();
    if (clientName.includes(input)) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}

//SWAL FOR LOGOUT
function confirmLogout() {
  Swal.fire({
    title: "Are you sure?",
    text: "You want to logout!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, logout!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "../php/logout.php"; // Redirect to logout
    }
  });
}
//SWAL FOR ADD ACCOUNT
function showAlert(title, text, icon, redirectUrl = null) {
  Swal.fire({
    title: title,
    text: text,
    icon: icon,
    confirmButtonText: "OK",
  }).then((result) => {
    if (result.isConfirmed && redirectUrl) {
      window.location.href = redirectUrl;
    }
  });
}

//sidebar mobile responsive
function openSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("show");
}

function closeSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.remove("show");
}

window.onclick = function (event) {
  const sidebar = document.getElementById("sidebar");
  const menuIcon = document.querySelector(".menu-icon");

  // Check if the clicked target is not the sidebar or the menu icon
  if (!sidebar.contains(event.target) && !menuIcon.contains(event.target)) {
    closeSidebar();
  }
};

// sort report daily,1 week, 1 month, 1 year, etc.
function updateReport() {
  var reportDuration = document.getElementById("report-duration").value;
  var baseUrl = window.location.pathname; // Get the current page URL
  var url = baseUrl + "?report_duration=" + reportDuration;

  if (reportDuration === "custom") {
    Swal.fire({
      title: "Select Date Range",
      html:
        'Start : <input type="date" id="start-date" class="swal2-input" placeholder="Start Date">' +
        '<br>End : <input type="date" id="end-date" class="swal2-input" placeholder="End Date">',
      focusConfirm: false,
      preConfirm: () => {
        var startDate = document.getElementById("start-date").value;
        var endDate = document.getElementById("end-date").value;
        if (!startDate || !endDate) {
          Swal.showValidationMessage("Please enter both start and end dates");
          return false;
        }
        url += "&start_date=" + startDate + "&end_date=" + endDate;
        window.location.href = url;
      },
    });
  } else {
    window.location.href = url;
  }
}

function showApproveSwal(appointmentId, isSanglaOrcr) {
  if (isSanglaOrcr) {
    // Show success message with redirecting timer for sangla-orcr
    let timerInterval;
    Swal.fire({
      title: "Please Wait a Moment",
      html: "Redirecting to Approving Section in <b>5</b> seconds.Thank you! ",
      icon: "information",
      confirmButtonText: "OK",
      allowOutsideClick: false,
      allowEscapeKey: false,
      timer: 5000,
      timerProgressBar: true,
      didOpen: () => {
        const b = Swal.getHtmlContainer().querySelector("b");
        timerInterval = setInterval(() => {
          b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
        }, 1000);
      },
      willClose: () => {
        clearInterval(timerInterval);
      },
    }).then(() => {
      // Submit the form after clicking OK
      document.getElementById(`approve-form-${appointmentId}`).submit();
    });

    // Automatically click the OK button after 5 seconds
    setTimeout(() => {
      Swal.clickConfirm();
    }, 5000);
  } else {
    // Show bank partner selection for other appointments
    Swal.fire({
      title: "Approve Client",
      html: `
          <label for="bank-partner">Select Bank Partner:</label>
          <select id="bank-partner" class="swal2-input">
              <option value="JACCS">JACCS</option>
              <option value="ORICO">ORICO</option>
              <option value="Banco De Oro">Banco De Oro</option>
              <option value="Security Bank">Security Bank</option>
              <option value="MayBank">MayBank</option>
          </select>
      `,
      focusConfirm: false,
      preConfirm: () => {
        const bankPartner = document.getElementById("bank-partner").value;
        if (!bankPartner) {
          Swal.showValidationMessage("Please select a bank partner");
          return false;
        }
        return { bankPartner };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        const { bankPartner } = result.value;
        // Set the hidden input values
        document.getElementById(`bank-partner-${appointmentId}`).value =
          bankPartner;

        // Show success message with redirecting timer
        let timerInterval;
        Swal.fire({
          title: "Successfully Approved",
          html: "Proceed to payment section, Thank you! Redirecting in <b>5</b> seconds.",
          icon: "success",
          confirmButtonText: "OK",
          allowOutsideClick: false,
          allowEscapeKey: false,
          timer: 5000,
          timerProgressBar: true,
          didOpen: () => {
            const b = Swal.getHtmlContainer().querySelector("b");
            timerInterval = setInterval(() => {
              b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 1000);
          },
          willClose: () => {
            clearInterval(timerInterval);
          },
        }).then(() => {
          // Submit the form after clicking OK
          document.getElementById(`approve-form-${appointmentId}`).submit();
        });

        // Automatically click the OK button after 5 seconds
        setTimeout(() => {
          Swal.clickConfirm();
        }, 5000);
      }
    });
  }
}

function showDeclineSwal(clientId) {
  Swal.fire({
    title: "Decline Client",
    text: "Click OK to proceed with declining the client.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Okay",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      let timerInterval;
      Swal.fire({
        title: "Proceed to Decline Client",
        html: "Please fill all the details for declining the client. Redirecting in <b>5</b> seconds.",
        icon: "success",
        confirmButtonText: "Okay",
        allowOutsideClick: false,
        allowEscapeKey: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: () => {
          const b = Swal.getHtmlContainer().querySelector("b");
          timerInterval = setInterval(() => {
            b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
          }, 1000);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      }).then(() => {
        // Submit the form after clicking OK
        document.getElementById(`decline-form-${clientId}`).submit();
      });

      // Automatically click the OK button after 5 seconds
      setTimeout(() => {
        Swal.clickConfirm();
      }, 5000);
    }
  });
}

function downloadAndPrintPDF() {
  const url = "?generate_pdf=1&report_duration=<?php echo $report_duration; ?>";
  fetch(url)
    .then((response) => response.blob())
    .then((blob) => {
      const url = URL.createObjectURL(blob);
      const iframe = document.createElement("iframe");
      iframe.style.display = "none";
      iframe.src = url;
      document.body.appendChild(iframe);
      iframe.onload = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
      };
    })
    .catch((error) => console.error("Error downloading PDF:", error));
}
