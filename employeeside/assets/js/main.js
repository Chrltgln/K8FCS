// hamburger.js
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
  
    hamburger.addEventListener('click', function () {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
    });
  
    // Disable past dates for maturity and check release inputs
    const today = new Date().toISOString().split('T')[0];
    const maturityInput = document.getElementById('maturity');
    const checkReleaseInput = document.getElementById('check-release');
  
    if (maturityInput) {
        maturityInput.setAttribute('min', today);
    }
  
    if (checkReleaseInput) {
        checkReleaseInput.setAttribute('min', today);
    }
  });
  
  // script.js
  document.querySelector(".add-files-button").addEventListener("click", () => {
      document.getElementById("uploadFileContainer").style.display = "block";
  });
  
  document.getElementById("closeButton").addEventListener("click", () => {
      document.getElementById("uploadFileContainer").style.display = "none";
  });
  
  window.addEventListener("click", (event) => {
      const uploadFileContainer = document.getElementById("uploadFileContainer");
      if (event.target === uploadFileContainer) {
          uploadFileContainer.style.display = "none";
      }
  });
  
  // search.js
  $(document).ready(function () {
      $("#search").on("input", function () {
          var searchTerm = $(this).val();
          $.ajax({
              url: "includes/searchAcceptedAppointments.php",
              type: "POST",
              data: { search: searchTerm },
              success: function (response) {
                  $("#appointments-list").html(response);
              },
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              },
          });
      });
  });
  
  // searchArchives.js
  $(document).ready(function () {
      $("#search").on("input", function () {
          var searchTerm = $(this).val();
          $.ajax({
              url: "includes/searchArchiveAppointments.php",
              type: "POST",
              data: { search: searchTerm },
              success: function (response) {
                  $("#appointments-list").html(response);
              },
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              },
          });
      });
  });
  
  // searchFiles.js
  $(document).ready(function () {
      $("#search").on("input", function () {
          var searchTerm = $(this).val();
          $.ajax({
              url: "includes/searchFiles.php",
              type: "POST",
              data: { search: searchTerm },
              success: function (response) {
                  $("#file-grid").html(response);
              },
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              },
          });
      });
  });
  
  // searchPending.js
  $(document).ready(function () {
      $("#search").on("input", function () {
          var searchTerm = $(this).val();
          $.ajax({
              url: "includes/searchPendingAppointments.php",
              type: "POST",
              data: { search: searchTerm },
              success: function (response) {
                  $("#pending-appointments-list").html(response);
              },
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              },
          });
      });
  });
  
  // searchCheckPayment.js
  $(document).ready(function () {
      $("#search").on("input", function () {
          var searchTerm = $(this).val();
          $.ajax({
              url: "includes/searchCheckPayment.php",
              type: "POST",
              data: { search: searchTerm },
              success: function (response) {
                  $("#check-payment-appointments-list").html(response);
              },
              error: function (xhr, status, error) {
                  console.error(xhr.responseText);
              },
          });
      });
  });
  
  
  // swal.js
   // Upload form submission
   $("#uploadForm").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
  
    $.ajax({
        url: $(this).attr("action"),
        type: $(this).attr("method"),
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (typeof response === "string") {
                response = JSON.parse(response);
            }
            if (response.success) {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: response.message,
                }).then(() => {
                    location.reload(); // Reload the page to show the new files
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: response.message,
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "An error occurred while uploading the file.",
            });
        },
    });
  });
  
  // Add event listener for the delete buttons
  $(".delete-button").on("click", function () {
    const fileName = $(this).data("file");
  
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'processes/delete-file.php',
                type: 'POST',
                data: { file: fileName },
                success: function (response) {
                    if (typeof response === "string") {
                        response = JSON.parse(response);
                    }
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page to show the updated file list
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred while deleting the file.",
                    });
                },
            });
        }
    });
  });
  
  
  