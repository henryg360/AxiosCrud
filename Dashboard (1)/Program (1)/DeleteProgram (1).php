<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

if (isset($_POST['delete_id'])) {
    $_SESSION['delete_id'] = $_POST['delete_id'];
}

$program = null;
if (isset($_SESSION['delete_id'])) {
    $program_id = $_SESSION['delete_id'];

    $dbh = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $dbh->prepare("SELECT * FROM programs WHERE progid = :program_id");
    $query->bindParam(':program_id', $program_id);
    $query->execute();
    $program = $query->fetch(PDO::FETCH_ASSOC);
}

if ($program === false || $program === null) {
    $_SESSION['message'] = "Program not found.";
    header("Location: ProgramListing.php");
    exit();
}

// Retrieve college name
$collegeQuery = $dbh->prepare("SELECT collfullname FROM colleges WHERE collid = :college_id");
$collegeQuery->bindParam(':college_id', $program['progcollid']);
$collegeQuery->execute();
$college = $collegeQuery->fetch(PDO::FETCH_ASSOC);

// Retrieve department name
$departmentQuery = $dbh->prepare("SELECT deptfullname FROM departments WHERE deptid = :department_id");
$departmentQuery->bindParam(':department_id', $program['progcolldeptid']);
$departmentQuery->execute();
$department = $departmentQuery->fetch(PDO::FETCH_ASSOC);

$errors = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Program</title>
    <link rel="stylesheet" href="../../Assets/DeletePrograms.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="DeleteProgram.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <table>
            <tr>
                <th colspan="2">Confirm Delete Program</th>
            </tr>
            <tr>
                <td>Program ID</td>
                <td><input type="text" id="programId" value="<?= htmlspecialchars($program['progid'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Program Name</td>
                <td><input type="text" id="programName" value="<?= htmlspecialchars($program['progfullname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Program Short Name</td>
                <td><input type="text" id="programShortName" value="<?= htmlspecialchars($program['progshortname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>College</td>
                <td><input type="text" id="collegeName" value="<?= htmlspecialchars($college['collfullname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td>Department</td>
                <td><input type="text" id="departmentName" value="<?= htmlspecialchars($department['deptfullname'] ?? '') ?>" readonly></td>
            </tr>
            <tr>
                <td colspan="2" class="button-row">
                    <button id="deletebtn">Delete</button>
                    <button id="cancelbtn" onclick="window.location.href='ProgramListing.php'">Cancel</button>
                </td>
            </tr>
        </table>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete this program? This action cannot be undone.</p>
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
                    <p id="successMessage">Program successfully deleted!</p>
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
