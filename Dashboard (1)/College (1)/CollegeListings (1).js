// Attach event listener to the logout button
document.addEventListener("DOMContentLoaded", () => {
    const logoutButton = document.getElementById("logoutbtn");
    const logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
    const confirmLogoutButton = document.getElementById("confirmLogoutBtn");
    const logoutForm = document.getElementById("logoutForm");

    // Show modal when logout button is clicked
    logoutButton.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default form submission
        logoutModal.show(); // Display the modal
    });

    // Submit the form when "Yes" is clicked in the modal
    confirmLogoutButton.addEventListener("click", () => {
        // Add a hidden input to properly identify the logout action
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "logout";
        logoutForm.appendChild(hiddenInput);

        logoutForm.submit(); // Submit the form
    });
});
