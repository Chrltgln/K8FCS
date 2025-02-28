let timeout;
const logoutTime = 300000; 
let alertifyVisible = false;


function startTimer() {
  if (!alertifyVisible) {
    timeout = setTimeout(logoutUser, logoutTime);
  }
}
function resetTimer() {
  clearTimeout(timeout);
  startTimer();
}


function logoutUser() {
  clearTimeout(timeout); 
  if (typeof alertify !== "undefined") {
    alertify.alert(
      "Session Timeout",
      "Your account has been automatically logged out due to inactivity.",
      function () {
        window.location.href = "../php/logout.php"; 
      }
    );
    alertifyVisible = true; 
    console.log("Alertify shown, timer stopped");
  } else {
    console.error("Alertify.js is not defined");
    window.location.href = "../php/logout.php"; 
  }
}

function hideAlertify() {
  if (typeof alertify !== "undefined") {
    alertify.dismissAll(); 
    alertifyVisible = false; 
    startTimer(); 
  }
}

// Event listeners for user activity
window.onload = function () {

  document.removeEventListener("mousemove", resetTimer);
  document.removeEventListener("keypress", resetTimer);
  document.removeEventListener("click", resetTimer);

  document.addEventListener("mousemove", resetTimer);
  document.addEventListener("keypress", resetTimer);
  document.addEventListener("click", resetTimer);

  startTimer();
};

// Clear timeout when the page is refreshed or unloaded
window.onbeforeunload = function () {
  clearTimeout(timeout);
  console.log("Timeout cleared on page unload");
};

// Function to disable specific keys
function disableKeys(e) {
  if (alertifyVisible) {
    const key = e.key || e.code;
    if (
      key === "F5" ||
      key === "F12" ||
      key === "ArrowLeft" ||
      key === "ArrowRight"
    ) {
      e.preventDefault();
      e.stopImmediatePropagation();
      console.log(`Disabled key: ${key}`);
    }

    if (e.ctrlKey && key === "r") {
      e.preventDefault();
      e.stopImmediatePropagation();
      console.log("Disabled key combination: Ctrl + R");
    }

    if (key === "Escape") {
      e.preventDefault();
      e.stopImmediatePropagation();
      console.log("Disabled key: Escape");
    }
  }
}

document.addEventListener("keydown", disableKeys);

document.addEventListener("alertify:shown", function () {
  alertifyVisible = true;
  clearTimeout(timeout); 
});

document.addEventListener("alertify:dismissed", function () {
  alertifyVisible = false;
  startTimer(); 
});
