document.addEventListener('DOMContentLoaded', async function () {
    const collegeDropdown = document.querySelector('select[name="studcollid"]');
    const programDropdown = document.querySelector('select[name="studprogid"]');

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
            } catch (error) {
                console.error("Error fetching programs:", error);
            }
        } else {
            programDropdown.innerHTML = '<option value="">Select Program</option>';
        }
    });
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

    if (!studid || !studfirstname || !studlastname || !studcollid || !studprogid || !studyear) {
        showErrors(["All fields are required."]);
        return;
    }

    try {
        const response = await axios.post('BackendAddStudent.php', {
            studid,
            studfirstname,
            studmidname,
            studlastname,
            studcollid,
            studprogid,
            studyear,
        });

        if (response.data.success) {
            alert("Student added successfully!");
            window.location.href = "StudentListing.php";
        } else if (Array.isArray(response.data.errors)) {
            showErrors(response.data.errors);
        }
    } catch (error) {
        console.error("Error response:", error.response);
        showErrors(["An unexpected error occurred: " + error.message]);
    }
});

function showErrors(errors) {
    const errorList = document.querySelector('.error-list');
    errorList.innerHTML = ''; // Clear previous errors
    errors.forEach(error => {
        const li = document.createElement('li');
        li.textContent = error;
        errorList.appendChild(li);
    });
    errorList.style.display = 'block'; // Show the error list
}
