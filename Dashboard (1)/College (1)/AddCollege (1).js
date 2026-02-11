document.getElementById('savebtn').addEventListener('click', async function (event) {
    event.preventDefault();

    const collegeId = document.querySelector('input[name="college_id"]').value.trim();
    const fullName = document.querySelector('input[name="full_name"]').value.trim();
    const shortName = document.querySelector('input[name="short_name"]').value.trim();

    if (!collegeId || !fullName || !shortName) {
        showModalErrors(["All fields are required."]);
        return;
    }
    try {
        const response = await axios.post('BackendAddCollege.php', {
            college_id: collegeId,
            full_name: fullName,
            short_name: shortName,
            save: true
        });

        if (response.data.success) {
            showModalSuccess("College added successfully!");
        } else {
            if (Array.isArray(response.data.errors)) {
                showModalErrors(response.data.errors);
            }
        }
    } catch (error) {
        console.error("Error response:", error.response); 
        showModalErrors(["An unexpected error occurred: " + error.message]);
    }
});

function showModalErrors(errors) {
    const errorList = document.getElementById('modalErrorList');
    errorList.innerHTML = ''; // Clear previous errors
    errors.forEach(error => {
        const li = document.createElement('li');
        li.textContent = error;
        errorList.appendChild(li);
    });

    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {
        backdrop: 'static', // Disable closing by clicking outside the modal
        keyboard: false // Disable closing with the keyboard
    });
    errorModal.show();
}

function showModalSuccess(message) {
    const successMessage = document.getElementById('successMessage');
    successMessage.textContent = message;

    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
        backdrop: 'static', // Disable closing by clicking outside the modal
        keyboard: false // Disable closing with the keyboard
    });
    successModal.show();

    document.getElementById('successOkBtn').addEventListener('click', () => {
        successModal.hide();
        window.location.href = "CollegeListings.php";
    });
}
