document.addEventListener('DOMContentLoaded', async function () {
    const programId = document.getElementById('programIdInput').value;
    const collegeDropdown = document.querySelector('select[name="college_id"]');
    const departmentDropdown = document.querySelector('select[name="department_id"]');

    let program;

    // Fetch program details
    try {
        const response = await axios.get(`BackendEditProgram.php?program_id=${programId}`);
        program = response.data;

        if (program) {
            document.querySelector('input[name="program_name"]').value = program.progfullname;
            document.querySelector('input[name="program_shortname"]').value = program.progshortname;
        }
    } catch (error) {
        console.error("Error fetching program details:", error);
    }

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

        // Set the current college
        collegeDropdown.value = program.progcollid;
        // Trigger change event to load departments
        const event = new Event('change');
        collegeDropdown.dispatchEvent(event);
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

                // Set the current department
                departmentDropdown.value = program.progcolldeptid;
            } catch (error) {
                console.error("Error fetching departments:", error);
            }
        } else {
            // Clear the department dropdown if no college is selected
            departmentDropdown.innerHTML = '<option value="">Select Department</option>';
        }
    });

    document.getElementById('savebtn').addEventListener('click', async function (event) {
        event.preventDefault();

        const programName = document.querySelector('input[name="program_name"]').value.trim();
        const programShortName = document.querySelector('input[name="program_shortname"]').value.trim();
        const collegeId = document.querySelector('select[name="college_id"]').value;
        const departmentId = document.querySelector('select[name="department_id"]').value;

        const errors = [];

        if (!programName) {
            errors.push("Program name is required.");
        }
        if (!programShortName) {
            errors.push("Program short name is required.");
        }
        if (!collegeId) {
            errors.push("Please select a college.");
        }
        if (!departmentId) {
            errors.push("Please select a department.");
        }

        if (errors.length > 0) {
            showErrors(errors);
            return;
        }

        try {
            const response = await axios.post('BackendEditProgram.php', {
                program_id: programId,
                program_name: programName,
                program_shortname: programShortName,
                college_id: collegeId,
                department_id: departmentId,
            });

            if (response.data.success) {
                showModalSuccess("Program updated successfully!");
            } else {
                const serverErrors = response.data.errors || ["An unknown error occurred."];
                showErrors(serverErrors);
            }
        } catch (error) {
            console.error("Error response:", error.response);
            showErrors(["An unexpected error occurred: " + error.message]);
        }
    });

    function showErrors(errors) {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.innerHTML = ''; // Clear previous errors
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorMessage.appendChild(li);
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

        document.getElementById("successOkBtn").addEventListener('click', () => {
            successModal.hide();
            window.location.href = "ProgramListing.php";
        });
    }
});
