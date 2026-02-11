<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

if (isset($_POST['delete_id'])) {
    $_SESSION['delete_id'] = $_POST['delete_id'];
}

$student = null;
if (isset($_SESSION['delete_id'])) {
    $student_id = $_SESSION['delete_id'];

    $dbh = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $dbh->prepare("SELECT * FROM students WHERE studid = :student_id");
    $query->bindParam(':student_id', $student_id);
    $query->execute();
    $student = $query->fetch(PDO::FETCH_ASSOC);
}

if ($student === false || $student === null) {
    $_SESSION['message'] = "Student not found.";
    header("Location: StudentListing.php");
    exit();
}

// Retrieve college name
$collegeQuery = $dbh->prepare("SELECT collfullname FROM colleges WHERE collid = :college_id");
$collegeQuery->bindParam(':college_id', $student['studcollid']);
$collegeQuery->execute();
$college = $collegeQuery->fetch(PDO::FETCH_ASSOC);

// Retrieve program name
$programQuery = $dbh->prepare("SELECT progfullname FROM programs WHERE progid = :program_id");
$programQuery->bindParam(':program_id', $student['studprogid']);
$programQuery->execute();
$program = $programQuery->fetch(PDO::FETCH_ASSOC);

$errors = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
    <link rel="stylesheet" href="../../Assets/DeleteStudents.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="DeleteStudent.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <table>
            <tr>
                <th colspan="2">Confirm Delete Student</th>
            </tr>
            <tr>
                <td>Student ID</td>
                <td><input type="text" id="studentId" value="<?= htmlspecialchars($student['studid'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>First Name</td>
                <td><input type="text" id="firstName" value="<?= htmlspecialchars($student['studfirstname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Middle Name</td>
                <td><input type="text" id="midName" value="<?= htmlspecialchars($student['studmidname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Last Name</td>
                <td><input type="text" id="lastName" value="<?= htmlspecialchars($student['studlastname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>College</td>
                <td><input type="text" id="collegeName" value="<?= htmlspecialchars($college['collfullname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Program</td>
                <td><input type="text" id="programName" value="<?= htmlspecialchars($program['progfullname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Year</td>
                <td><input type="number" id="year" value="<?= htmlspecialchars($student['studyear'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td colspan="2" class="button-row">
                    <button id="deletebtn">Delete</button>
                    <button id="cancelbtn" onclick="window.location.href='StudentListing.php'">Cancel</button>
                </td>
            </tr>
        </table>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete this student? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                    <p id="successMessage">Student successfully deleted!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="successOkBtn">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
