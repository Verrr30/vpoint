// Toggle password visibility
function togglePassword(fieldId) {
  const passwordInput = document.getElementById(fieldId);
  const toggleIcon =
    passwordInput.parentNode.querySelector(".toggle-password i");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    toggleIcon.classList.remove("fa-eye");
    toggleIcon.classList.add("fa-eye-slash");
  } else {
    passwordInput.type = "password";
    toggleIcon.classList.remove("fa-eye-slash");
    toggleIcon.classList.add("fa-eye");
  }
}

// Form validation
document.getElementById("loginForm").addEventListener("submit", function (e) {
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  let isValid = true;

  // Reset previous error states
  document.querySelectorAll(".error-message").forEach((el) => el.remove());
  document
    .querySelectorAll(".error-input")
    .forEach((el) => el.classList.remove("error-input"));

  // Email validation
  if (!email || !isValidEmail(email)) {
    showError("email", "Email tidak valid");
    isValid = false;
  }

  // Password validation
  if (!password) {
    showError("password", "Password tidak boleh kosong");
    isValid = false;
  }

  if (!isValid) {
    e.preventDefault();
  }
});

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function showError(fieldId, message) {
  const field = document.getElementById(fieldId);
  field.classList.add("error-input");

  const errorDiv = document.createElement("div");
  errorDiv.className = "error-message";
  errorDiv.style.color = "#dc2626";
  errorDiv.style.fontSize = "0.8em";
  errorDiv.style.marginTop = "5px";
  errorDiv.textContent = message;

  field.parentNode.appendChild(errorDiv);
}

// Add loading state to button when form is submitted
document.getElementById("loginForm").addEventListener("submit", function (e) {
  if (this.checkValidity()) {
    const button = this.querySelector('button[type="submit"]');
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    button.disabled = true;
  }
});

// Register form validation
document
  .getElementById("registerForm")
  ?.addEventListener("submit", function (e) {
    const username = document.getElementById("username").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    let isValid = true;

    // Reset previous error states
    document.querySelectorAll(".error-message").forEach((el) => el.remove());
    document
      .querySelectorAll(".error-input")
      .forEach((el) => el.classList.remove("error-input"));

    // Username validation
    if (!username || username.length < 3) {
      showError("username", "Username minimal 3 karakter");
      isValid = false;
    }

    // Email validation
    if (!email || !isValidEmail(email)) {
      showError("email", "Email tidak valid");
      isValid = false;
    }

    // Password validation
    if (!password || password.length < 6) {
      showError("password", "Password minimal 6 karakter");
      isValid = false;
    }

    // Confirm password validation
    if (password !== confirmPassword) {
      showError("confirm_password", "Password tidak cocok");
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
