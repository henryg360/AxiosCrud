<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="../../Assets/AddStudent.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="AddStudent.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form id="studentForm">
            <table>
                <tr>
                    <th colspan="2">Student Information Data Entry</th>
                </tr>

                <tr>
                    <td colspan="2">
                        <ul class="error-list" style="color: red; display: none;"></ul>
                    </td>
                </tr>

                <tr>
                    <td>Student ID</td>
                    <td><input type="number" name="studid" required></td>
                </tr>
                <tr>
                    <td>First Name</td>
                    <td><input type="text" name="studfirstname" required></td>
                </tr>
                <tr>
                    <td>Middle Name</td>
                    <td><input type="text" name="studmidname"></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type="text" name="studlastname" required></td>
                </tr>
                <tr>
                    <td>College</td>
                    <td>
                        <select name="studcollid" required>
                            <option value="">Select College</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Program</td>
                    <td>
                        <select name="studprogid" required>
                            <option value="">Select Program</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Year</td>
                    <td><input type="number" name="studyear" min="1" max="5" required></td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <button type="submit" id="savebtn">Save</button>
                        <input type="reset" value="Clear Entries" id="clearbtn">
                        <button type="button" id="cancelbtn" onclick="window.location.href='StudentListing.php'">Cancel</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    <ul id="errorMessage"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    <p id="successMessage">Student added successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="successOkBtn">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
