$(document).ready(function () {
  // alert('hiii');
  $.noConflict();
  // Custom rule for email or phone validation
  $.validator.addMethod('emailOrPhone', function (value, element) {
    // Regular expression for email validation
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Regular expression for phone number validation (you may need to adjust this)
    var phoneRegex = /^\d{10}$/;

    return emailRegex.test(value) || phoneRegex.test(value);
  }, 'Please enter a valid email or phone number');
  $("#loginform").validate({
    rules: {
      email: {
        required: true,
        emailOrPhone: true
      },
      password: {
        required: true,
        // minlength: 8,
        // strongPassword: true
      }
    },
    messages: {
      email: {
        required: "Please enter your email address or phone number",
        emailOrPhone: "Please enter a valid email or phone number",
      },
      password: {
        required: "Please enter your password",

      }
    },
    highlight: function (element) {
      $(element).addClass("error-border");

      $(element).removeClass("success-border");
    },
    unhighlight: function (element) {
      $(element).removeClass("error-border");
      $(element).addClass("success-border");
    }
  });

  // Add a CSS class for error border
  // $.validator.addClassRules("error-border", {
  //   errorClass: "input-error"
  // });
  $("#forgotpasswordform").validate({
    rules: {
      emailOrPhone: {
        required: true,
        emailOrPhone: true
      }
    },
    messages: {
      emailOrPhone: {
        required: 'Please enter your email or phone number'
      }

    },
    highlight: function (element) {
      $(element).addClass("error-border");

      $(element).removeClass("success-border");
    },
    unhighlight: function (element) {
      $(element).removeClass("error-border");
      $(element).addClass("success-border");
    },
    submitHandler: function (form) {
      // Your form submission logic goes here
      form.submit();
    }
  });



  /**
   * otp verifiction 
   *  validation 
   * function 
   */


  $("#otpform").validate({
    rules: {
      "otp[]": {
        required: true,
        digits: true,
        minlength: 1,
        maxlength: 1,
      },
    },
    messages: {
      "otp[]": {
        required: "Please enter all OTP digits.",
        digits: "Please enter only digits.",
        minlength: "Please enter all OTP digits.",
        maxlength: "Please enter all OTP digits.",
      },
    },
    errorPlacement: function (error, element) {
      $('.otpError').html(error);
      //error.appendTo(element.closest(".input-field"));
    },
    submitHandler: function (form) {
      form.submit();
    },
  });


  // change password validation
  $("#changePassword").validate({
    rules: {
      email: {
        required: true,
        email: true
      },
      password: {
        required: true,
        minlength: 8,
        strongPassword: true
      },
      password_confirmation: {
        required: true,
        equalTo: "#password"
      }
    },
    messages: {
      email: {
        required: "Please enter your email address",
        email: "Please enter a valid email address"
      },
      password: {
        required: "Please enter your password",
        minlength: "Password must be at least 8 characters long",
        strongPassword: "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character",
      },
      password_confirmation: {
        required: "Please enter your Confirm password",
        equalTo: "Confirm password can't match with Password "
        // strongPassword: "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character",
      }
    },
    highlight: function (element) {
      $(element).addClass("error-border");

      $(element).removeClass("success-border");
    },
    unhighlight: function (element) {
      $(element).removeClass("error-border");
      $(element).addClass("success-border");
    }

  });

  $.validator.addMethod("strongPassword", function (value, element) {
    return this.optional(element) ||
      /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/.test(value);
  }, "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character");

  const inputs = document.querySelectorAll(".input-field input"),
    button = document.querySelector("button");

  inputs.forEach((input, index1) => {
    input.addEventListener("input", () => {
      const currentInput = input;
      const inputValue = currentInput.value;

      // Use a regular expression to remove any non-numeric characters
      const numericValue = inputValue.replace(/[^0-9]/g, "");

      // Update the input value to only contain numeric characters
      currentInput.value = numericValue;

      if (index1 < inputs.length - 1 && numericValue !== "") {
        inputs[index1 + 1].focus();
      }

      // Check if all inputs have a single numeric character
      const validInputs = Array.from(inputs).every((input) => input.value.length === 1);

      if (validInputs) {
        button.classList.add("active");
      } else {
        button.classList.remove("active");
      }
    });

    input.addEventListener("keydown", (e) => {
      if (e.key === "Backspace" && index1 > 0 && inputs[index1].value === "") {
        // Check if the current input is the fourth one and it's empty
        // Clear the fourth input value and keep focus on it
        inputs[index1].value = "";
        inputs[index1 - 1].focus();
      }
    });
  });
  window.addEventListener("load", () => inputs[0].focus());

  // To fade out success or error message
  $(".errorclass").fadeIn();
  setTimeout(function () {
    $(".errorclass").fadeOut();
  }, 4000);

  $(".successclass").fadeIn();
  setTimeout(function () {
    $(".successclass").fadeOut();
  }, 4000);


});


// For otp pase into the boxes 
$('input[name="otp[]"]:first').on('paste', function (e) {
  var pastedText = e.originalEvent.clipboardData.getData('text');
  if (/^\d{4}$/.test(pastedText)) {
    var otpDigits = pastedText.split('');
    $('input[name="otp[]"]').each(function (index) {
      if (index < otpDigits.length) {
        $(this).val(otpDigits[index]);
      }
      $('input[name="otp[]"]:last').focus();

    });
  }
});