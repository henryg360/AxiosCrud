document.addEventListener('DOMContentLoaded', async function () {
    const studentId = document.getElementById('studentIdInput').value;
    const collegeDropdown = document.querySelector('select[name="studcollid"]');
    const programDropdown = document.querySelector('select[name="studprogid"]');

    let student;

    // Fetch student details
    try {
        const response = await axios.get(`BackendEditStudent.php?student_id=${studentId}`);
        student = response.data;

        if (student) {
            document.querySelector('input[name="studid"]').value = student.studid;
            document.querySelector('input[name="studfirstname"]').value = student.studfirstname;
            document.querySelector('input[name="studmidname"]').value = student.studmidname;
            document.querySelector('input[name="studlastname"]').value = student.studlastname;
        }
    } catch (error) {
        console.error("Error fetching student details:", error);
    }

    // Populate colleges dropdown
    try {
        const response = await axios.get('BackendAddStudent.php');
        const colleges = response.data;

        colleges.forEach(college => {
            const option = document.createElement('option');
            option.value = college.collid;
            option.textContent = college.collfullname;
            collegeDropdown.appendChild(option);
        });

        // Set the current college
        collegeDropdown.value = student.studcollid;
        // Trigger change event to load programs
        const event = new Event('change');
        collegeDropdown.dispatchEvent(event);
    } catch (error) {
        console.error("Error fetching colleges:", error);
    }

    // Fetch programs when a college is selected
    collegeDropdown.addEventListener('change', async function () {
        const collegeId = collegeDropdown.value;
        if (collegeId) {
            try {
                const response = await axios.get(`BackendAddStudent.php?collegeId=${collegeId}`);
                const programs = response.data;

                // Clear previous program options
                programDropdown.innerHTML = '<option value="">Select Program</option>';

                programs.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.progid;
                    option.textContent = program.progfullname;
                    programDropdown.appendChild(option);
                });

                // Set the current program
                programDropdown.value = student.studprogid;
            } catch (error) {
                console.error("Error fetching programs:", error);
            }
        } else {
            // Clear the program dropdown if no college is selected
            programDropdown.innerHTML = '<option value="">Select Program</option>';
        }
    });

    document.getElementById('savebtn').addEventListener('click', async function (event) {
        event.preventDefault();

        const studid = document.querySelector('input[name="studid"]').value.trim();
        const studfirstname = document.querySelector('input[name="studfirstname"]').value.trim();
        const studmidname = document.querySelector('input[name="studmidname"]').value.trim();
        const studlastname = document.querySelector('input[name="studlastname"]').value.trim();
        const studcollid = document.querySelector('select[name="studcollid"]').value;
        const studprogid = document.querySelector('select[name="studprogid"]').value;
        const studyear = document.querySelector('input[name="studyear"]').value;

        const errors = [];

        if (!studcollid) {
            errors.push("Please select a college.");
        }
        if (!studprogid) {
            errors.push("Please select a program.");
        }
        if (!studyear) {
            errors.push("Please select a year.");
        }
        if (!studfirstname) {
            errors.push("First name is required.");
        }
        if (!studlastname) {
            errors.push("Last name is required.");
        }

        if (errors.length > 0) {
            showErrors(errors);
            return;
        }

        try {
            const response = await axios.post('BackendEditStudent.php', {
                student_id: studentId,
                studid,
                studfirstname,
                studmidname,
                studlastname,
                studcollid,
                studprogid,
                studyear,
            });

            if (response.data.success) {
                showModalSuccess("Student updated successfully!");
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
            window.location.href = "StudentListing.php";
        });
    }
});
