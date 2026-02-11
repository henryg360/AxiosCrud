document.addEventListener('DOMContentLoaded', async function () {
    const collegeDropdown = document.querySelector('select[name="college_id"]');

    // Populate colleges dropdown
    try {
        const response = await axios.get('BackendAddDepartment.php');
        const colleges = response.data;

        colleges.forEach(college => {
            const option = document.createElement('option');
            option.value = college.collid;
            option.textContent = college.collfullname;
            collegeDropdown.appendChild(option);
        });
    } catch (error) {
        console.error("Error fetching colleges:", error);
    }
});

document.getElementById('savebtn').addEventListener('click', async function (event) {
    event.preventDefault();

    const deptId = document.querySelector('input[name="dept_id"]').value.trim();
    const fullName = document.querySelector('input[name="full_name"]').value.trim();
    const shortName = document.querySelector('input[name="short_name"]').value.trim();
    const collegeId = document.querySelector('select[name="college_id"]').value;

    let errors = [];

    if (!collegeId) {
        errors.push("Please select a college.");
    }

    if (!deptId || !fullName || !shortName) {
        errors.push("All fields are required.");
    }

    if (errors.length > 0) {
        showModalErrors(errors);
        return;
    }

    try {
        const response = await axios.post('BackendAddDepartment.php', {
            dept_id: deptId,
            full_name: fullName,
            short_name: shortName,
            college_id: collegeId,
        });

        if (response.data.success) {
            showModalSuccess("Department added successfully!");
        } else if (Array.isArray(response.data.errors)) {
            showModalErrors(response.data.errors);
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
        window.location.href = "DepartmentListing.php";
    });
}
