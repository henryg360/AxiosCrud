<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

if (isset($_POST['edit_id'])) {
    $_SESSION['edit_id'] = $_POST['edit_id'];
}

$student = null;
if (isset($_SESSION['edit_id'])) {
    $student_id = $_SESSION['edit_id'];

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

// Retrieve colleges for the dropdown
$collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve programs for the current college
$programs = [];
if ($student) {
    $programsQuery = $dbh->prepare("SELECT progid, progfullname FROM programs WHERE progcollid = :college_id");
    $programsQuery->bindParam(':college_id', $student['studcollid']);
    $programsQuery->execute();
    $programs = $programsQuery->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../../Assets/EditStudent.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="EditStudent.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form id="studentForm">
            <input type="hidden" id="studentIdInput" value="<?= htmlspecialchars($_SESSION['edit_id'] ?? '') ?>">
            <table>
                <tr>
                    <th colspan="2">Edit Student Information</th>
                </tr>

                <tr>
                    <td colspan="2">
                        <ul class="error-list" style="color: red; display: none;"></ul>
                    </td>
                </tr>

                <tr>
                    <td>Student ID</td>
                    <td><input type="text" name="studid" value="<?= htmlspecialchars($student['studid'] ?? '') ?>" readonly></td>
                </tr>
                <tr>
                    <td>First Name</td>
                    <td><input type="text" name="studfirstname" value="<?= htmlspecialchars($student['studfirstname'] ?? '') ?>" required></td>
                </tr>
                <tr>
                    <td>Middle Name</td>
                    <td><input type="text" name="studmidname" value="<?= htmlspecialchars($student['studmidname'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input type="text" name="studlastname" value="<?= htmlspecialchars($student['studlastname'] ?? '') ?>" required></td>
                </tr>
                <tr>
                    <td>College</td>
                    <td>
                        <select name="studcollid" required>
                            <option value="">Select College</option>
                            <?php foreach ($colleges as $college): ?>
                                <option value="<?= $college['collid'] ?>" <?= $college['collid'] == ($student['studcollid'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($college['collfullname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Program</td>
                    <td>
                        <select name="studprogid" required>
                            <option value="">Select Program</option>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= $program['progid'] ?>" <?= $program['progid'] == ($student['studprogid'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($program['progfullname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Year</td>
                    <td><input type="number" name="studyear" min="1" max="5" value="<?= htmlspecialchars($student['studyear'] ?? '') ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <button type="submit" id="savebtn">Save</button>
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
                    <p id="successMessage">Student successfully updated!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="successOkBtn">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
