document.addEventListener("DOMContentLoaded", () => {
  const pages = document.querySelectorAll(".form-page");
  const nextButtons = document.querySelectorAll('[id^="next-page-"]');
  const prevButtons = document.querySelectorAll('[id^="prev-page-"]');
  const ownershipSelect = document.getElementById("ownership");
  const ownershipOtherInput = document.getElementById("ownership-other");
  const incomeSourceRadios = document.getElementsByName("income_source");
  const incomeSourceOtherInput = document.getElementById("income-source-other");
  const dobInput = document.getElementById("dob");
  const formErrorMessage = document.getElementById("form-error-message");
  const submitButton = document.querySelector('button[type="submit"]');
  const errorNote = document.getElementById("error-note");

  let currentPage = 0;

  // Function to show the current page and hide others
  function showPage(pageIndex) {
    pages.forEach((page, index) => {
      page.style.display = index === pageIndex ? "block" : "none";
    });
  }

  // Function to calculate age
  function calculateAge(dob) {
    const birthDate = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age;
  }

  // Function to validate the current page
  function validatePage(pageIndex) {
    const inputs = pages[pageIndex].querySelectorAll("input, select, textarea");
    let isValid = true;
    let errorMessages = [];

    inputs.forEach(input => {
      const trimmedValue = input.value.trim();
      if (trimmedValue && !input.checkValidity()) {
        isValid = false;
        input.parentElement.classList.add("error");
        input.parentElement.classList.remove("valid");
        if (input.validity.patternMismatch) {
          errorMessages.push("");
        } else if (input.type === "email" && !input.value.includes("@")) {
          errorMessages.push("");
        }
      } else if (trimmedValue) {
        input.parentElement.classList.remove("error");
        input.parentElement.classList.add("valid");
      }
    });

    const contactNumber1Input = document.getElementById("contact-number-1");
    const contactNumber2Input = document.getElementById("contact-number-2");
    const contactNumberInput = document.getElementById("contact-number-borrower");

    if (contactNumber1Input && contactNumber2Input && contactNumber1Input.value === contactNumber2Input.value) {
      isValid = false;
      contactNumber1Input.parentElement.classList.add("error");
      contactNumber2Input.parentElement.classList.add("error");
      const errorMessage2 = contactNumber2Input.nextElementSibling;
      if (errorMessage2) {
        errorMessage2.textContent = "Contact number already used";
      }
    }

    if (contactNumberInput && (contactNumberInput.value === contactNumber1Input.value || contactNumberInput.value === contactNumber2Input.value)) {
      isValid = false;
      contactNumberInput.parentElement.classList.add("error");
      const errorMessage = contactNumberInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "Contact number already used";
      }
    }

    formErrorMessage.innerHTML = errorMessages.join("<br>");

    return isValid;
  }

  // Function to validate specific field lengths
  function validateFieldLengths() {
    const creditCardInput = document.querySelector("#credit-card");
    const tinIdInput = document.querySelector("#tin-id");
    const sssNumberInput = document.querySelector("#sss-number");
    const tinNumberBorrowerInput = document.querySelector("#tin_number_borrower");
    const sssNumberBorrowerInput = document.querySelector("#sss_number_borrower");
    const creditCardBorrowerInput = document.querySelector("#credit-cards-borrower");

    let isValid = true;

    if (creditCardInput && creditCardInput.value.trim() && creditCardInput.value.length !== 16) {
      isValid = false;
      creditCardInput.parentElement.classList.add("error");
      creditCardInput.parentElement.classList.remove("valid");
    }

    if (tinIdInput && tinIdInput.value.trim() && tinIdInput.value.length !== 12) {
      isValid = false;
      tinIdInput.parentElement.classList.add("error");
      tinIdInput.parentElement.classList.remove("valid");
    }

    if (sssNumberInput && sssNumberInput.value.trim() && sssNumberInput.value.length !== 10) {
      isValid = false;
      sssNumberInput.parentElement.classList.add("error");
      sssNumberInput.parentElement.classList.remove("valid");
    }

    if (tinNumberBorrowerInput && tinNumberBorrowerInput.value.trim() && tinNumberBorrowerInput.value.length !== 12) {
      isValid = false;
      tinNumberBorrowerInput.parentElement.classList.add("error");
      tinNumberBorrowerInput.parentElement.classList.remove("valid");
      const errorMessage = tinNumberBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "Please enter exactly 12 digits.";
      }
    } else if (tinNumberBorrowerInput && tinNumberBorrowerInput.value.trim()) {
      tinNumberBorrowerInput.parentElement.classList.remove("error");
      tinNumberBorrowerInput.parentElement.classList.add("valid");
      const errorMessage = tinNumberBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "";
      }
    }

    if (sssNumberBorrowerInput && sssNumberBorrowerInput.value.trim() && sssNumberBorrowerInput.value.length !== 10) {
      isValid = false;
      sssNumberBorrowerInput.parentElement.classList.add("error");
      sssNumberBorrowerInput.parentElement.classList.remove("valid");
      const errorMessage = sssNumberBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "Please enter exactly 10 digits.";
      }
    } else if (sssNumberBorrowerInput && sssNumberBorrowerInput.value.trim()) {
      sssNumberBorrowerInput.parentElement.classList.remove("error");
      sssNumberBorrowerInput.parentElement.classList.add("valid");
      const errorMessage = sssNumberBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "";
      }
    }

    if (creditCardBorrowerInput && creditCardBorrowerInput.value.trim() && creditCardBorrowerInput.value.length !== 16) {
      isValid = false;
      creditCardBorrowerInput.parentElement.classList.add("error");
      creditCardBorrowerInput.parentElement.classList.remove("valid");
      const errorMessage = creditCardBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "Please enter exactly 16 digits.";
      }
    } else if (creditCardBorrowerInput && creditCardBorrowerInput.value.trim()) {
      creditCardBorrowerInput.parentElement.classList.remove("error");
      creditCardBorrowerInput.parentElement.classList.add("valid");
      const errorMessage = creditCardBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "";
      }
    }

    return isValid;
  }

  // Function to check if contact number already exists
  function contactNumberExists(contactNumber) {
    // Replace this with actual logic to check if the contact number exists
    // For example, you might make an AJAX request to the server to check
    return false; // Placeholder: always returns false
  }

  // Function to check if contact number is unique
  function isContactNumberUnique(contactNumber) {
    const contactNumbers = document.querySelectorAll("#contact-number-borrower, #contact-number-1, #contact-number-2");
    let count = 0;
    contactNumbers.forEach(input => {
      if (input.value === contactNumber) {
        count++;
      }
    });
    return count <= 1;
  }

  // Function to check if contact number is already used
  function isContactNumberUsed(contactNumber) {
    const contactNumber1 = document.getElementById("contact-number-1");
    return contactNumber1 && contactNumber1.value === contactNumber;
  }

  // Event listener for Submit button
  submitButton.addEventListener("click", (event) => {
    const contactNumberInput = document.getElementById("contact-number-borrower");
    const contactNumber1Input = document.getElementById("contact-number-1");
    const contactNumber2Input = document.getElementById("contact-number-2");
    const tinNumberBorrowerInput = document.getElementById("tin_number_borrower");
    const sssNumberBorrowerInput = document.getElementById("sss_number_borrower");
    const creditCardBorrowerInput = document.getElementById("credit-cards-borrower");

    if (contactNumberInput && contactNumberExists(contactNumberInput.value)) {
      event.preventDefault();
      alertify.alert("Form Submission Error", "The contact number already exists. Please use a different number.");
      return;
    }

    if (contactNumberInput && !isContactNumberUnique(contactNumberInput.value)) {
      event.preventDefault();
      alertify.alert("Form Submission Error", "The contact number must be unique. Please use a different number.");
      return;
    }

    if (contactNumber2Input && (isContactNumberUsed(contactNumber2Input.value) || contactNumber2Input.value === contactNumberInput.value)) {
      event.preventDefault();
      alertify.alert("Form Submission Error", "Contact number 2 must be different from contact number 1 and contact number borrower.");
      return;
    }

    if (contactNumberInput && (contactNumberInput.value === contactNumber1Input.value || contactNumberInput.value === contactNumber2Input.value)) {
      event.preventDefault();
      alertify.alert("Form Submission Error", "Contact number of the co-borrower must be different from Contact number 1 and Contact number 2 of the primary borrower.");
      return;
    }

    if (!validatePage(currentPage) || !validateFieldLengths()) {
      event.preventDefault();

      // Display the AlertifyJS alert for missing fields or invalid inputs
      alertify.alert("Form Submission Error", "Please fill up all the fields correctly to submit the form.");

      // Move and position the AlertifyJS container
      setTimeout(() => {
        const alertifyContainer = document.querySelector(".alertify");
        if (alertifyContainer) {
          alertifyContainer.style.position = "absolute"; // Ensure it appears above other elements
        }
      }, 0); // Delay to ensure the alert container is created
    } else if (document.querySelector('.form-group.error')) {
      event.preventDefault();

      // Display the AlertifyJS alert for invalid inputs
      alertify.alert("Form Submission Error", "There are invalid inputs in the form. Please correct them first before submitting.");

      // Move and position the AlertifyJS container
      setTimeout(() => {
        const alertifyContainer = document.querySelector(".alertify");
        if (alertifyContainer) {
          alertifyContainer.style.position = "absolute"; // Ensure it appears above other elements
        }
      }, 0); // Delay to ensure the alert container is created
    } else {
      event.preventDefault();

      // Display the SweetAlert2 confirmation message
      Swal.fire({
        title: "Are you sure?",
        text: "Please confirm that all your details are correct before submitting.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Submit",
        cancelButtonText: "Cancel",
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Submit the form programmatically
          document.querySelector('#application-form').submit();
        }
      });
    }
  });

  // Add input event listeners to display error messages in real-time
  const tinNumberBorrowerInput = document.getElementById("tin_number_borrower");
  const sssNumberBorrowerInput = document.getElementById("sss_number_borrower");
  const creditCardBorrowerInput = document.getElementById("credit-cards-borrower");

  if (tinNumberBorrowerInput) {
    tinNumberBorrowerInput.addEventListener("input", () => {
      const errorMessage = tinNumberBorrowerInput.nextElementSibling;
      if (tinNumberBorrowerInput.value !== "" && tinNumberBorrowerInput.value.length !== 12) {
        tinNumberBorrowerInput.parentElement.classList.add("error");
        tinNumberBorrowerInput.parentElement.classList.remove("valid");
        if (errorMessage) {
          errorMessage.textContent = "Please enter exactly 12 digits.";
        }
      } else {
        tinNumberBorrowerInput.parentElement.classList.remove("error");
        tinNumberBorrowerInput.parentElement.classList.add("valid");
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }
    });
  }

  if (sssNumberBorrowerInput) {
    sssNumberBorrowerInput.addEventListener("input", () => {
      const errorMessage = sssNumberBorrowerInput.nextElementSibling;
      if (sssNumberBorrowerInput.value !== "" && sssNumberBorrowerInput.value.length !== 10) {
        sssNumberBorrowerInput.parentElement.classList.add("error");
        sssNumberBorrowerInput.parentElement.classList.remove("valid");
        if (errorMessage) {
          errorMessage.textContent = "Please enter exactly 10 digits.";
        }
      } else {
        sssNumberBorrowerInput.parentElement.classList.remove("error");
        sssNumberBorrowerInput.parentElement.classList.add("valid");
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }
    });
  }

  if (creditCardBorrowerInput) {
    creditCardBorrowerInput.addEventListener("input", () => {
      const errorMessage = creditCardBorrowerInput.nextElementSibling;
      if (creditCardBorrowerInput.value !== "" && creditCardBorrowerInput.value.length !== 16) {
        creditCardBorrowerInput.parentElement.classList.add("error");
        creditCardBorrowerInput.parentElement.classList.remove("valid");
        if (errorMessage) {
          errorMessage.textContent = "Please enter exactly 16 digits.";
        }
      } else {
        creditCardBorrowerInput.parentElement.classList.remove("error");
        creditCardBorrowerInput.parentElement.classList.add("valid");
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }
    });
  }

  // Function to validate exact number requirements for TIN and SSS numbers
  function validateExactNumberRequirements() {
    const tinNumberInput = document.querySelector("#tin_number");
    const sssNumberInput = document.querySelector("#sss_number");
    const contactNumberInput = document.querySelector("#contact-number-borrower");
    const tinNumberBorrowerInput = document.querySelector("#tin_number_borrower");
    const sssNumberBorrowerInput = document.querySelector("#sss_number_borrower");

    let isValid = true;

    if (tinNumberInput && tinNumberInput.value !== "" && tinNumberInput.value.length !== 12) {
      isValid = false;
      tinNumberInput.parentElement.classList.add("error");
      tinNumberInput.parentElement.classList.remove("valid");
    }

    if (sssNumberInput && sssNumberInput.value !== "" && sssNumberInput.value.length !== 10) {
      isValid = false;
      sssNumberInput.parentElement.classList.add("error");
      sssNumberInput.parentElement.classList.remove("valid");
    }

    if (contactNumberInput && contactNumberInput.value !== "" && contactNumberInput.value.length !== 11) {
      isValid = false;
      contactNumberInput.parentElement.classList.add("error");
      contactNumberInput.parentElement.classList.remove("valid");
    }

    if (tinNumberBorrowerInput && tinNumberBorrowerInput.value !== "" && tinNumberBorrowerInput.value.length !== 12) {
      tinNumberBorrowerInput.parentElement.classList.add("error");
      tinNumberBorrowerInput.parentElement.classList.remove("valid");
      const errorMessage = tinNumberBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "Please enter exactly 12 digits.";
      }
    }

    if (sssNumberBorrowerInput && sssNumberBorrowerInput.value !== "" && sssNumberBorrowerInput.value.length !== 10) {
      sssNumberBorrowerInput.parentElement.classList.add("error");
      sssNumberBorrowerInput.parentElement.classList.remove("valid");
      const errorMessage = sssNumberBorrowerInput.nextElementSibling;
      if (errorMessage) {
        errorMessage.textContent = "Please enter exactly 10 digits.";
      }
    }

    return isValid;
  }

  // Function to disable past dates in the appointment date input field
  function disablePastDates() {
    const appointmentDateInput = document.getElementById("appointment-date");
    if (appointmentDateInput) {
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
      const dd = String(today.getDate()).padStart(2, '0');
      const minDate = `${yyyy}-${mm}-${dd}`;
      appointmentDateInput.setAttribute("min", minDate);
    }
  }

  // Call the function to disable past dates
  disablePastDates();

  // Event listener for Next buttons
  nextButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (validatePage(currentPage) && currentPage < pages.length - 1) {
        currentPage++;
        showPage(currentPage);
      }
      window.scrollTo(0, 0); // Scroll to the top of the page
    });
  });

  // Event listener for Previous buttons
  prevButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (currentPage > 0) {
        currentPage--;
        showPage(currentPage);
      }
      window.scrollTo(0, 0); // Scroll to the top of the page
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
      } else {
        incomeSourceOtherInput.style.display = "none";
      }
    });
  });

  // Initialize form by showing the first page
  showPage(currentPage);

  // Add blur event listeners to inputs for real-time validation
  const inputs = document.querySelectorAll("input, select, textarea");
  inputs.forEach(input => {
    input.addEventListener("blur", () => {
      const errorMessage = input.nextElementSibling;
      if (!input.checkValidity()) {
        input.parentElement.classList.add("error");
        input.parentElement.classList.remove("valid");
        if (errorMessage) {
          if (input.validity.patternMismatch) {
            errorMessage.textContent = "Please enter exactly 11 digits.";
          } else if (input.type === "email" && !input.value.includes("@")) {
            errorMessage.textContent = "Please enter a valid email address.";
          }
        }
      } else {
        input.parentElement.classList.remove("error");
        input.parentElement.classList.add("valid");
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }
    });

    // Add input event listener to clear error message when the field becomes valid
    input.addEventListener("input", () => {
      if (input.checkValidity()) {
        input.parentElement.classList.remove("error");
        input.parentElement.classList.add("valid");
        const errorMessage = input.nextElementSibling;
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }
    });
  });

  // Add blur event listener to ownership select for real-time validation
  ownershipSelect.addEventListener("blur", () => {
    if (!ownershipSelect.checkValidity()) {
      ownershipSelect.parentElement.classList.add("error");
      ownershipSelect.parentElement.classList.remove("valid");
    } else {
      ownershipSelect.parentElement.classList.remove("error");
      ownershipSelect.parentElement.classList.add("valid");
    }
  });

  // Add input event listener to ownership select to clear error message when the field becomes valid
  ownershipSelect.addEventListener("input", () => {
    if (ownershipSelect.checkValidity()) {
      ownershipSelect.parentElement.classList.remove("error");
      ownershipSelect.parentElement.classList.add("valid");
    }
  });
});


// error - validation
document.addEventListener("DOMContentLoaded", () => {
    const nextButtons = document.querySelectorAll('#next-page-1, #next-page-2, #next-page-3, #next-page-4');
    const prevButtons = document.querySelectorAll('#prev-page-2, #prev-page-3, #prev-page-4, #prev-page-5');
    const form = document.querySelector('#application-form');
    const ownershipSelect = document.querySelector('#ownership');
    const ownershipOtherInput = document.querySelector('#ownership-other');

    // Show or hide the "ownership-other" input field based on the selected option
    ownershipSelect.addEventListener('change', () => {
        if (ownershipSelect.value === 'Others') {
            ownershipOtherInput.style.display = 'block';
            ownershipOtherInput.setAttribute('required', 'required');
        } else {
            ownershipOtherInput.style.display = 'none';
            ownershipOtherInput.removeAttribute('required');
        }
    });

    function validatePage(button) {
      let isValid = true;
      const currentPage = button.closest('.form-page');
      const requiredFields = currentPage.querySelectorAll('input[required], select[required], textarea[required]');
      const contactNumberPattern = /^\d{11}$/;
      const creditCardPattern = /^\d{16}$/;
      const tinNumberPattern = /^\d{12}$/;
      const sssNumberPattern = /^\d{10}$/;
    
      requiredFields.forEach(field => {
        const formGroup = field.closest('.form-group');
        const errorMessage = formGroup.querySelector('.error-message');
    
        if (!field.checkValidity() || 
            !validateDOB(field) || !validateAppointmentDate(field) || 
            (field.id === "dob" || field.id === "date-of-birth-borrower") && !validateFutureDate(field) || 
            (field.id === "contact-number-borrower" || field.id === "contact-number-1" || field.id === "contact-number-2") && !contactNumberPattern.test(field.value) || 
            (field.id === "credit-cards") && !creditCardPattern.test(field.value) || 
            (field.id === "tin_number") && !tinNumberPattern.test(field.value) || 
            (field.id === "sss_number") && !sssNumberPattern.test(field.value)) {
          
          isValid = false;
          formGroup.classList.add('error');
          formGroup.classList.remove('valid');
          
          if (errorMessage && field.dataset.touched) {
            if (field.validity.rangeOverflow || field.validity.rangeUnderflow) {
              errorMessage.textContent = `Please enter a year between ${field.min} and ${field.max}.`;
            } else if (field.id === "contact-number-borrower" || field.id === "contact-number-1" || field.id === "contact-number-2") {
              errorMessage.textContent = "Please enter exactly 11 digits.";
            } else if (field.id === "credit-cards") {
              errorMessage.textContent = "Please enter exactly 16 digits.";
            } else if (field.id === "tin_number") {
              errorMessage.textContent = "Please enter exactly 12 digits.";
            } else if (field.id === "sss_number") {
              errorMessage.textContent = "Please enter exactly 10 digits.";
            } else if (field.type === "email" && !field.value.includes("@")) {
              errorMessage.textContent = "Please enter a valid email address.";
            } else if ((field.id === "dob") && !validateDOB(field)) {
              errorMessage.textContent = "You must be at least 18 years old.";
            } else if ((field.id === "date-of-birth-borrower") && !validateDOB(field)) {
              errorMessage.textContent = "Co-borrower must be at least 18 years old.";
            } else if ((field.id === "dob" || field.id === "date-of-birth-borrower") && !validateFutureDate(field)) {
              errorMessage.textContent = "The date cannot be in the future.";
            } else if (field.id === "appointment-date" && !validateAppointmentDate(field)) {
              errorMessage.textContent = "Date not available.";
            } else {
              errorMessage.textContent = "Invalid input.";
            }
          }
        } else {
          formGroup.classList.remove('error');
          formGroup.classList.add('valid');
          if (errorMessage) {
            errorMessage.textContent = "";
          }
        }
      });

      const contactNumber1Input = document.getElementById("contact-number-1");
      const contactNumber2Input = document.getElementById("contact-number-2");
      const contactNumberInput = document.getElementById("contact-number-borrower");
  
      if (contactNumber1Input && contactNumber2Input && contactNumber1Input.value === contactNumber2Input.value) {
        isValid = false;
        contactNumber1Input.parentElement.classList.add("error");
        contactNumber2Input.parentElement.classList.add("error");
        const errorMessage1 = contactNumber1Input.nextElementSibling;
        const errorMessage2 = contactNumber2Input.nextElementSibling;

        if (errorMessage2) {
          errorMessage2.textContent = "Contact number already used";
        }
      }

      if (contactNumberInput && (contactNumberInput.value === contactNumber1Input.value || contactNumberInput.value === contactNumber2Input.value)) {
        isValid = false;
        contactNumberInput.parentElement.classList.add("error");
        const errorMessage = contactNumberInput.nextElementSibling;
        if (errorMessage) {
          errorMessage.textContent = "Contact number already used";
        }
      }
    
      return isValid;
    }

    function validateDOB(input) {
      if (input.id !== "dob" && input.id !== "date-of-birth-borrower") return true;
      const dob = new Date(input.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Set time to midnight to compare only dates
  
      // Calculate age
      let age = today.getFullYear() - dob.getFullYear();
      const birthDateThisYear = new Date(today.getFullYear(), dob.getMonth(), dob.getDate());
      if (today < birthDateThisYear) {
          age--;
      }
  
      // Check if the date of birth is in the future
      const isFutureDate = dob.getTime() > today.getTime();

      return age >= 18 && !isFutureDate;
  }

    function validateFutureDate(input) {
        const date = new Date(input.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Set time to midnight to compare only dates
        return date.getTime() <= today.getTime();
    }

    function validateAppointmentDate(input) {
      if (input.id !== "appointment-date") return true;
      const appointmentDate = new Date(input.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Set time to midnight to compare only dates
    
      const maxDate = new Date();
      maxDate.setMonth(today.getMonth() + 2); // Set the maximum date to two months from today
    
      if (appointmentDate < today) {
        input.setCustomValidity("Date not available.");
        return false;
      } else if (appointmentDate > maxDate) {
        input.setCustomValidity("The appointment date must be within the next one month.");
        return false;
      } else {
        input.setCustomValidity(""); // Clear any previous error message
        return true;
      }
    }

    nextButtons.forEach(button => {
      button.addEventListener('click', () => {
          const currentPage = button.closest('.form-page');
          const formErrorMessage = currentPage.querySelector('#form-error-message');
          if (validatePage(button)) {
              currentPage.style.display = 'none';
              currentPage.nextElementSibling.style.display = 'block';
              if (formErrorMessage) {
                  formErrorMessage.textContent = '';
              }
          } else {
              if (formErrorMessage) {
                  formErrorMessage.textContent = 'Please fill out all the required fields.';
              }
          }
      });
  });
  
  prevButtons.forEach(button => {
      button.addEventListener('click', () => {
          const currentPage = button.closest('.form-page');
          const formErrorMessage = currentPage.querySelector('#form-error-message');
          if (validatePage(button)) {
              currentPage.style.display = 'none';
              currentPage.previousElementSibling.style.display = 'block';
              if (formErrorMessage) {
                  formErrorMessage.textContent = '';
              }
          } else {
              if (formErrorMessage) {
                  formErrorMessage.textContent = 'Please fill out all the required fields.';
              }
          }
      });
  });

    const inputs = document.querySelectorAll("input, select, textarea");
    inputs.forEach(input => {
        input.addEventListener("blur", () => {
            input.dataset.touched = true;
            validateInput(input);
        });

        input.addEventListener("input", () => {
            validateInput(input);
        });

        // Restrict input to numeric characters for specific fields
        input.addEventListener("keypress", (event) => {
            if (input.id === "contact-number-1" || input.id === "contact-number-2") {
                const charCode = event.charCode;
                if (charCode < 48 || charCode > 57) {
                    event.preventDefault();
                }
            }
        });
    });

    // Prevent form submission on Enter key press
    form.addEventListener("keypress", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });

    function validateInput(input) {
      const formGroup = input.closest('.form-group');
      const errorMessage = formGroup ? formGroup.querySelector('.error-message') : null;
      const contactNumberPattern = /^\d{11}$/;
      const creditCardPattern = /^\d{16}$/;
      const tinNumberPattern = /^\d{12}$/;
      const sssNumberPattern = /^\d{10}$/;

      let isValid = true;
      const trimmedValue = input.value.trim();

      if (trimmedValue && (!input.checkValidity() || 
          ((input.id === "dob" || input.id === "date-of-birth-borrower") && (!validateDOB(input) || !validateFutureDate(input))) || 
          (input.id === "appointment-date" && !validateAppointmentDate(input)) || 
          ((input.id === "contact-number-borrower" || input.id === "contact-number-1" || input.id === "contact-number-2") && !contactNumberPattern.test(trimmedValue)) || 
          ((input.id === "credit-cards") && !creditCardPattern.test(trimmedValue)) || 
          ((input.id === "tin_number") && trimmedValue !== "" && !tinNumberPattern.test(trimmedValue)) || 
          ((input.id === "sss_number") && trimmedValue !== "" && !sssNumberPattern.test(trimmedValue)))) {
        
        isValid = false;
        formGroup.classList.add("error");
        formGroup.classList.remove("valid");
        
        if (errorMessage && input.dataset.touched) {
          if (input.validity.rangeOverflow || input.validity.rangeUnderflow) {
            errorMessage.textContent = `Please enter a year between ${input.min} and ${input.max}.`;
          } else if (input.id === "contact-number-borrower" || input.id === "contact-number-1" || input.id === "contact-number-2") {
            errorMessage.textContent = "Please enter exactly 11 digits.";
          } else if (input.id === "credit-cards") {
            errorMessage.textContent = "Please enter exactly 16 digits.";
          } else if (input.id === "tin_number") {
            errorMessage.textContent = "Please enter exactly 12 digits.";
          } else if (input.id === "sss_number") {
            errorMessage.textContent = "Please enter exactly 10 digits.";
          } else if (input.type === "email" && !input.value.includes("@")) {
            errorMessage.textContent = "Please enter a valid email address.";
          } else if ((input.id === "dob") && !validateDOB(input)) {
            errorMessage.textContent = "You must be at least 18 years old.";
          } else if ((input.id === "date-of-birth-borrower") && !validateDOB(input)) {
            errorMessage.textContent = "Co-borrower must be at least 18 years old.";
          } else if ((input.id === "dob" || input.id === "date-of-birth-borrower") && !validateFutureDate(input)) {
            errorMessage.textContent = "Date not available.";
          } else if (input.id === "appointment-date" && !validateAppointmentDate(input)) {
            errorMessage.textContent = "Date not available.";
          } else {
            errorMessage.textContent = "Invalid input.";
          }
          console.log(`Error message for ${input.id}: ${errorMessage.textContent}`);
        }
      } else if (trimmedValue) {
        formGroup.classList.remove("error");
        formGroup.classList.add("valid");
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }

      const contactNumber1Input = document.getElementById("contact-number-1");
      const contactNumber2Input = document.getElementById("contact-number-2");
      const contactNumberInput = document.getElementById("contact-number-borrower");
  
      if (contactNumber1Input && contactNumber2Input && contactNumber1Input.value === contactNumber2Input.value) {
        contactNumber1Input.parentElement.classList.add("error");
        contactNumber2Input.parentElement.classList.add("error");
        const errorMessage2 = contactNumber2Input.nextElementSibling;
        if (errorMessage2) {
          errorMessage2.textContent = "Contact number already used";
        }
      }

      if (contactNumberInput && (contactNumberInput.value === contactNumber1Input.value || contactNumberInput.value === contactNumber2Input.value)) {
        contactNumberInput.parentElement.classList.add("error");
        const errorMessage = contactNumberInput.nextElementSibling;
        if (errorMessage) {
          errorMessage.textContent = "Contact number already used";
        }
      }
    
      // Remove form group error if the input is not required and is blank
      if (!input.required && trimmedValue === "") {
        formGroup.classList.remove("error");
        formGroup.classList.add("valid");
      }
    
      // Always remove the error message if the input is blank
      if (trimmedValue === "") {
        if (errorMessage) {
          errorMessage.textContent = "";
        }
      }
    
      return isValid;
    }
});
