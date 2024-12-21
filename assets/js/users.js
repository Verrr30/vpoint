// Function to update user status
function updateUserStatus(userId, status) {
  fetch("update-status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `user_id=${userId}&status=${status ? 1 : 0}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Status user berhasil diupdate!", "success");
      } else {
        showNotification("Gagal mengupdate status: " + data.message, "error");
        // Revert toggle if failed
        document.getElementById(`status-${userId}`).checked = !status;
      }
    })
    .catch((error) => {
      showNotification("Error: " + error, "error");
      // Revert toggle if error
      document.getElementById(`status-${userId}`).checked = !status;
    });
}

// Show notification function
function showNotification(message, type) {
  const notification = document.createElement("div");
  notification.className = `alert alert-${type}`;
  notification.textContent = message;

  // Add to page
  const contentWrapper = document.querySelector(".content-wrapper");
  contentWrapper.insertBefore(notification, contentWrapper.firstChild);

  // Remove after 3 seconds
  setTimeout(() => {
    notification.remove();
  }, 3000);
}

// Initialize tooltips
document.addEventListener("DOMContentLoaded", function () {
  const tooltips = document.querySelectorAll("[title]");
  tooltips.forEach((tooltip) => {
    new Tooltip(tooltip);
  });
});
