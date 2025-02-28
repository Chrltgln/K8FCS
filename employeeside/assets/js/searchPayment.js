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
  