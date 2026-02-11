document.addEventListener("DOMContentLoaded", () => {
    const deleteBtn = document.getElementById("deletebtn");
    const cancelBtn = document.getElementById("cancelbtn");
    const confirmationModal = new bootstrap.Modal(document.getElementById("confirmationModal"));

    deleteBtn.addEventListener("click", (e) => {
        e.preventDefault();
        confirmationModal.show();
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
        const collegeId = document.getElementById("college_id").value;
        
        axios.post("BackendDeleteCollege.php", { college_id: collegeId })
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
                showModalErrors(["An error occurred while deleting the college."]);
            });
    });

    cancelBtn.addEventListener("click", (e) => {
        e.preventDefault();
        window.location.href = "CollegeListings.php";
    });
});

function showModalErrors(errors) {
    const errorList = document.getElementById("modalErrorList");
    errorList.innerHTML = ''; // Clear previous errors
    errors.forEach(error => {
        const li = document.createElement("li");
        li.textContent = error;
        errorList.appendChild(li);
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
        window.location.href = "CollegeListings.php";
    });
}
