document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
    const modalMessage = document.getElementById("modalMessage");

    // Display server error message if present
    if (serverErrorMessage.trim() !== "") {
        modalMessage.textContent = serverErrorMessage;
        errorModal.show();

        // Clear only the password field if the error is "Incorrect password"
        if (serverErrorMessage === "Incorrect password.") {
            document.getElementById("password").value = "";
        }
    }

    // Client-side validation before form submission
    loginForm.addEventListener("submit", (event) => {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!username && !password) {
            event.preventDefault(); // Stop form submission
            modalMessage.textContent = "Please Input username and password.";
            errorModal.show();
        } else if (!username) {
            event.preventDefault(); // Stop form submission
            modalMessage.textContent = "Please Input your username.";
            errorModal.show();
        } else if (!password) {
            event.preventDefault(); // Stop form submission
            modalMessage.textContent = "Please Input your password.";
            errorModal.show();
        }
    });
});
