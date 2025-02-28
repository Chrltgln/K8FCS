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
