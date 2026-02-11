// Ensure the DOM is fully loaded before initializing modals
document.addEventListener("DOMContentLoaded", () => {
    const logoutModal = document.getElementById("logoutModal");

    // Event listener for showing the modal (if needed for custom behavior)
    if (logoutModal) {
        logoutModal.addEventListener("show.bs.modal", () => {
            console.log("Logout modal is shown");
        });

        // Event listener for closing the modal
        logoutModal.addEventListener("hide.bs.modal", () => {
            console.log("Logout modal is hidden");
        });
    }
});
