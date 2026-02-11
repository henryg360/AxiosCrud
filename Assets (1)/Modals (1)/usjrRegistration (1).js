document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registrationForm");
    const errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
    const modalMessage = document.getElementById("modalMessage");
    const modalOkButton = document.getElementById("modalOkButton");

    form.addEventListener("submit", (event) => {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm_password").value;

        // Check password requirements
        if (!username && !password && !confirmPassword) {
            event.preventDefault();
            modalMessage.textContent = "Please input username and password.";
            errorModal.show();
        } else if (!username) {
            event.preventDefault();
            modalMessage.textContent = "Please input username.";
            errorModal.show();
        } else if (!password) {
            event.preventDefault();
            modalMessage.textContent = "Please input password.";
            errorModal.show();
        } else if (!confirmPassword) {
            event.preventDefault();
            modalMessage.textContent = "Please confirm your password.";
            errorModal.show();
        } else if (password.length < 8 || !/[A-Za-z]/.test(password) || !/[0-9]/.test(password)) {
            event.preventDefault();
            modalMessage.textContent = "Password must be at least 8 characters long and include at least one letter and one number.";
            errorModal.show();
        } else if (password !== confirmPassword) {
            event.preventDefault();
            modalMessage.textContent = "Passwords do not match.";
            errorModal.show();
        } else if (errorMessage) {
            event.preventDefault();
            modalMessage.textContent = errorMessage;
            errorModal.show();
        }
    });

    // Success message handling
    if (successMessage) {
        modalMessage.textContent = successMessage;
        errorModal.show();

        modalOkButton.addEventListener("click", () => {
            window.location.href = "usjrLogin.php";
        });
    }
});
