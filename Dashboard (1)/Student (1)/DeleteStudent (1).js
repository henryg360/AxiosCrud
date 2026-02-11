document.addEventListener("DOMContentLoaded", () => {
    const deleteBtn = document.getElementById("deletebtn");
    const cancelBtn = document.getElementById("cancelbtn");
    const confirmationModal = new bootstrap.Modal(document.getElementById("confirmationModal"));

    deleteBtn.addEventListener("click", (e) => {
        e.preventDefault();
        confirmationModal.show();
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
        console.log("Confirmation modal hidden, proceeding with delete operation");
        confirmationModal.hide();
        const studentId = document.getElementById("studentId").value;

        axios.post("BackendDeleteStudent.php", { student_id: studentId })
            .then((response) => {
                const res = response.data;
                if (res.success) {
                    showModalSuccess("Student successfully deleted!");
                } else {
                    showModalErrors([res.errors.join("\n")]);
                }
            })
            .catch((error) => {
                console.error("Error during delete operation:", error);
                showModalErrors(["An error occurred while deleting the student."]);
            });
    });

    cancelBtn.addEventListener("click", (e) => {
        e.preventDefault();
        window.location.href = "StudentListing.php";
    });
});

function showModalErrors(errors) {
    const errorMessage = document.getElementById("errorMessage");
    errorMessage.innerHTML = ''; // Clear previous error
    errors.forEach(error => {
        const p = document.createElement("p");
        p.textContent = error;
        errorMessage.appendChild(p);
    });

    console.log("Displaying error modal with errors:", errors);
    const errorModal = new bootstrap.Modal(document.getElementById("errorModal"), {
        backdrop: "static", // Disable closing by clicking outside the modal
        keyboard: false // Disable closing with the keyboard
    });
    errorModal.show();
}

function showModalSuccess(message) {
    const successMessage = document.getElementById("successMessage");
    successMessage.textContent = message;

    console.log("Displaying success modal with message:", message);
    const successModal = new bootstrap.Modal(document.getElementById("successModal"), {
        backdrop: "static", // Disable closing by clicking outside the modal
        keyboard: false // Disable closing with the keyboard
    });
    successModal.show();

    document.getElementById("successOkBtn").addEventListener("click", () => {
        successModal.hide();
        window.location.href = "StudentListing.php";
    });
}
