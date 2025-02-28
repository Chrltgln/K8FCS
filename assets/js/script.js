window.addEventListener("scroll", reveal);

function reveal() {
  var reveals = document.querySelectorAll(".reveal");

  for (var i = 0; i < reveals.length; i++) {
    var windowheight = window.innerHeight;
    var revealtop = reveals[i].getBoundingClientRect().top;
    var revealpoint = 150;

    if (revealtop < windowheight - revealpoint) {
      reveals[i].classList.add("active");
    }
  }
}

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
 
    this.classList.toggle("activeaccordion");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
} 

// Remove SWAL alert for login
$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get("error");

  if (error) {
    let errorMessage = '';
    if (error === "missing_field") {
      errorMessage = "Please fill all required fields.";
    } else if (error === "wrong_credentials") {
      errorMessage = "Incorrect email or password.";
    }
    document.getElementById("error-message").innerText = errorMessage;
  }
});

// SWAL alert for Unauthorized
$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const unauthorized = urlParams.get("unauthorized");

   if (unauthorized) {
    let title, text;
    if (unauthorized === "not_logged_in") {
      title = "Unauthorized Access";
      text = "You need to log in to access this page.";
    } else if (unauthorized === "wrong_role") {
      title = "Unauthorized Access";
      text = "You do not have permission to access this page.";
    }
    Swal.fire({
      icon: "error",
      title: title,
      text: text,
      confirmButtonText: "Okay",
    });
  }
});


// SWAL alert for Forgot Password
$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const forgotPasswordMessage = urlParams.get("forgot-password-message");

  if (forgotPasswordMessage) {
    let title = "Information";
    let icon = "info";

    switch (decodeURIComponent(forgotPasswordMessage)) {
      case "Check your email for the password reset link.":
        title = "Email sent successfully";
        icon = "success";
        break;
      case "Email not found.":
        title = "Error";
        icon = "error";
        break;
      case "Failed to send password reset email.":
        title = "Error";
        icon = "error";
        break;
      case "Invalid email format.":
        title = "Error";
        icon = "error";
        break;
      default:
        title = "Forgot Password";
        icon = "info";
    }

    Swal.fire({
      icon: icon,
      title: title,
      text: decodeURIComponent(forgotPasswordMessage),
      confirmButtonText: "OK",
    });
  }
});

//SWAL alert for Reset password
document.getElementById("reset-password-form").addEventListener("submit", function (e) {
  e.preventDefault();

  var password = document.getElementById("password").value;
  var passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  if (!passwordPattern.test(password)) {
    Swal.fire({
      icon: "error",
      title: "Invalid Password",
      text: "Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.",
      confirmButtonText: "OK",
      allowOutsideClick: false,
    });
  } else {
    // Proceed with form submission
    var formData = new FormData(this);

    fetch("process_reset_password.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Success",
            text: data.message,
            confirmButtonText: "OK",
            allowOutsideClick: false,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = "../php/login.php";
            }
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.message,
            confirmButtonText: "OK",
            allowOutsideClick: false,
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "An unexpected error occurred.",
          confirmButtonText: "OK",
          allowOutsideClick: false,
        });
      });
  }
});


