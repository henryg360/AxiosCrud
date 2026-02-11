document.getElementById("editDepartmentForm").addEventListener("submit", function (event) {
    // Check if the "Cancel" button was clicked
    const cancelBtnClicked = event.submitter && event.submitter.name === "Cancel";
    if (cancelBtnClicked) {
        window.location.href = "DepartmentListing.php";
        return;
    }

    event.preventDefault();

    const deptId = document.getElementById("deptId").value.trim();
    const fullName = document.getElementById("fullName").value.trim();
    const shortName = document.getElementById("shortName").value.trim();
    const collegeId = document.getElementById("collegeId").value.trim();

    let errors = [];

    if (!collegeId) {
        errors.push("Please select a college.");
    }

    if (!fullName) {
        errors.push("Department Name required.");
    }

    if (errors.length > 0) {
        showModalErrors(errors);
        return;
    }

    axios.post("BackendEditDepartment.php", {
        dept_id: deptId,
        full_name: fullName,
        short_name: shortName,
        college_id: collegeId,
    }).then(response => {
        if (response.data.success) {
            showModalSuccess("Department updated successfully!");
        } else {
            showModalErrors(response.data.errors || ["An unexpected error occurred."]);
        }
    }).catch(error => {
        console.error("Error:", error);
        showModalErrors(["An unexpected error occurred: " + error.message]);
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
        window.location.href = "DepartmentListing.php";
    });
}
