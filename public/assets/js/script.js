// Simple client-side helpers

// Flash message auto-hide
document.addEventListener("DOMContentLoaded", () => {
  const notices = document.querySelectorAll(".notice, .error");
  notices.forEach(n => {
    setTimeout(() => {
      n.style.transition = "opacity 1s";
      n.style.opacity = "0";
    }, 5000);
  });
});

// Confirm before logout
const logoutLinks = document.querySelectorAll("a[href*='logout.php']");
logoutLinks.forEach(link => {
  link.addEventListener("click", e => {
    if (!confirm("Are you sure you want to log out?")) {
      e.preventDefault();
    }
  });
});

// Highlight required fields
const requiredInputs = document.querySelectorAll("input[required], textarea[required], select[required]");
requiredInputs.forEach(input => {
  input.addEventListener("invalid", () => {
    input.style.borderColor = "red";
  });
  input.addEventListener("input", () => {
    input.style.borderColor = "#ccc";
  });
});