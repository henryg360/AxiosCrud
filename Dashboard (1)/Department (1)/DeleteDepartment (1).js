document.addEventListener("DOMContentLoaded", () => {
    const deleteBtn = document.getElementById("deletebtn");
    const cancelBtn = document.getElementById("cancelbtn");
    const confirmationModal = new bootstrap.Modal(document.getElementById("confirmationModal"));

    deleteBtn.addEventListener("click", (e) => {
        e.preventDefault();
        confirmationModal.show();
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
        confirmationModal.hide();
        const deptId = document.getElementById("deptId").value;

        axios.post("BackendDeleteDepartment.php", { dept_id: deptId })
            .then((response) => {
                const res = response.data;
                if (res.status === "success") {
                    showModalSuccess(res.message);
                } else {
                    showModalErrors([res.message]);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                showModalErrors(["An error occurred while deleting the department."]);
            });
    });

    cancelBtn.addEventListener("click", (e) => {
        e.preventDefault();
        window.location.href = "DepartmentListing.php";
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

    const errorModal = new bootstrap.Modal(document.getElementById("errorModal"), {
        backdrop: "static", // Disable closing by clicking outside the modal
        keyboard: false // Disable closing with the keyboard
    });
    errorModal.show();
}

function showModalSuccess(message) {
    const successMessage = document.getElementById("successMessage");
    successMessage.textContent = message;

    const successModal = new bootstrap.Modal(document.getElementById("successModal"), {
        backdrop: "static", // Disable closing by clicking outside the modal
        keyboard: false // Disable closing with the keyboard
    });
    successModal.show();

    document.getElementById("successOkBtn").addEventListener("click", () => {
        successModal.hide();
        window.location.href = "DepartmentListing.php";
    });
}
