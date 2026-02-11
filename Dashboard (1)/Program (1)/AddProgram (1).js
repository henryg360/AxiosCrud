document.addEventListener('DOMContentLoaded', async function () {
    const collegeDropdown = document.querySelector('select[name="college_id"]');
    const departmentDropdown = document.querySelector('select[name="department_id"]');

    // Populate colleges dropdown
    try {
        const response = await axios.get('BackendAddProgram.php');
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

    // Fetch departments when a college is selected
    collegeDropdown.addEventListener('change', async function () {
        const collegeId = collegeDropdown.value;
        if (collegeId) {
            try {
                const response = await axios.get(`BackendAddProgram.php?college_id=${collegeId}`);
                const departments = response.data;

                // Clear previous department options
                departmentDropdown.innerHTML = '<option value="">Select Department</option>';

                departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.deptid;
                    option.textContent = department.deptfullname;
                    departmentDropdown.appendChild(option);
                });
            } catch (error) {
                console.error("Error fetching departments:", error);
            }
        } else {
            // Clear the department dropdown if no college is selected
            departmentDropdown.innerHTML = '<option value="">Select Department</option>';
        }
    });
});

document.getElementById('savebtn').addEventListener('click', async function (event) {
    event.preventDefault();

    const programId = document.querySelector('input[name="program_id"]').value.trim();
    const programName = document.querySelector('input[name="program_name"]').value.trim();
    const programShortName = document.querySelector('input[name="program_shortname"]').value.trim();
    const collegeId = document.querySelector('select[name="college_id"]').value;
    const departmentId = document.querySelector('select[name="department_id"]').value;

    const errors = [];

    if (!programId) errors.push("Program ID is required.");
    if (!programName) errors.push("Program Name is required.");
    if (!programShortName) errors.push("Program Short Name is required.");
    if (!collegeId) errors.push("College is required.");
    if (!departmentId) errors.push("Department is required.");

    if (errors.length > 0) {
        if (errors.length === 5) {
            showMessage('Error', ["All fields are required."]);
        } else {
            showMessage('Error', errors);
        }
        return;
    }

    try {
        // Check for duplicate Program ID
        const duplicateCheckResponse = await axios.get(`BackendAddProgram.php?program_id_check=${programId}`);
        if (duplicateCheckResponse.data.exists) {
            showMessage('Error', ["Program ID must be unique."]);
            return;
        }

        const response = await axios.post('BackendAddProgram.php', {
            program_id: programId,
            program_name: programName,
            program_shortname: programShortName,
            college_id: collegeId,
            department_id: departmentId,
        });

        if (response.data.success) {
            showMessage('Success', ["Program added successfully!"], () => {
                window.location.href = "ProgramListing.php";
            });
        } else if (Array.isArray(response.data.errors)) {
            showMessage('Error', response.data.errors);
        }
    } catch (error) {
        console.error("Error response:", error.response);
        showMessage('Error', ["An unexpected error occurred: " + error.message]);
    }
});

function showMessage(title, messages, callback) {
    const messageModalLabel = document.getElementById('messageModalLabel');
    const messageList = document.getElementById('messageList');

    messageModalLabel.textContent = title;
    messageList.innerHTML = ''; // Clear previous messages
    messages.forEach(message => {
        const li = document.createElement('li');
        li.textContent = message;
        messageList.appendChild(li);
    });

    $('#messageModal').modal('show');

    $('#messageModal').on('hidden.bs.modal', function (e) {
        if (callback) {
            callback();
        }
    });
}
