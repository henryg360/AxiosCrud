document.getElementById("editCollegeForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    const collegeId = document.getElementById("collegeId").value.trim();
    const fullName = document.getElementById("fullName").value.trim();
    const shortName = document.getElementById("shortName").value.trim();

    if (!collegeId || !fullName || !shortName) {
        showModalErrors(["All fields are required."]);
        return;
    }

    try {
        const response = await axios.post("BackendEditCollege.php", {
            college_id: collegeId,
            full_name: fullName,
            short_name: shortName,
        });

        if (response.data.success) {
            showModalSuccess("College updated successfully!");
        } else {
            if (Array.isArray(response.data.errors)) {
                showModalErrors(response.data.errors);
            } else {
                showModalErrors(["An unexpected error occurred."]);
            }
        }
    } catch (error) {
        console.error("Error:", error);
        showModalErrors(["An unexpected error occurred: " + error.message]);
    }
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
